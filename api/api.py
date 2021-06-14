from hashlib import new
import flask
from flask import request, jsonify,g
from ldap3.protocol.convert import substring_to_dict
from pyasn1.type.univ import Null
import yaml
import ipaddress
import wgtools
import sqlite3

DATABASE = '/tmp/wgapi.db'
def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DATABASE)
    return db

def query_db(query, args=(), one=False):
    cur = get_db().execute(query, args)
    rv = cur.fetchall()
    cur.close()
    return (rv[0] if rv else None) if one else rv

def loadconfig():
    with open(r'config.yaml') as file:
        config = yaml.load(file, Loader=yaml.FullLoader)
    global config_ldap,config_vpn
    config_ldap=config['ldap']
    config_vpn=config['vpn']

def ldaplogin(host,user,dn,psw):
    from ldap3 import Server, Connection, ALL
    s = Server(host, get_info=ALL) 
    c = Connection(s, user=f'cn={user},{dn}', password=psw)
    if not c.bind():
        return False
    else:
        return True
loadconfig()
if  'public key' not in wgtools.show(config_vpn['device']) :
    wgtools.set(config_vpn['device'],listen_port=config_vpn['port'],private_key=config_vpn['private_key'])
server_public_key=wgtools.show(config_vpn['device'])['public key']
app = flask.Flask(__name__)
app.config["DEBUG"] = True

@app.route('/', methods=['GET', 'POST'])
def home():
    if request.method == 'POST':
        return request.args
    if request.method == 'GET':
        return "as"


@app.route('/login',methods=['POST'])
def login():
    if request.method == 'POST':
        loginRequest=request.json[0]
        user=loginRequest['user']
        passwd=loginRequest['password']
        if not ldaplogin('167.99.221.214',user,'ou=usuarios,dc=ldap,dc=ivan,dc=site',passwd) :
            return jsonify(STATUS = (43, "NOT AUTHORIZED"))        
        loadconfig()
        new_ip=None
        reserved_ips=list()
        cur = get_db().cursor()
        cur.execute('''CREATE TABLE IF NOT EXISTS conn
            (usuario text PRIMARY KEY, pubkey text,ip int UNIQUE NOT NULL)''')
        for ip in query_db('select ip from conn'):
            ip=int(ip[0])
            reserved_ips.append(ipaddress.ip_address(ip))
        for ip in config_vpn['reserved-ips'] :
            reserved_ips.extend(ipaddress.ip_network(ip).hosts())
        avaliable_ip=set(ipaddress.ip_network(config_vpn['vpn-subnet']).hosts())-set(reserved_ips)
        try:
            new_ip=str(next(iter(avaliable_ip)))
        except StopIteration :
            return jsonify({"error": "No hay IP libres"})
        public_key, private_key = wgtools.keypair()
        STATUS = (10, "OK")
        DATA = {
        "server_ip" : config_vpn['ip'],
        "host" : config_vpn['host'],
        "port" : config_vpn['port'],
        "server_public_key": server_public_key,
        "private_key": private_key,
        "ip": new_ip,
        "route_networks": config_vpn['routing-subnets'],
        }
        DEBUG = { 'request' : request.json}
        response={'status' : STATUS, 'data' : DATA, 'debug' : DEBUG}
    wgtools.set(config_vpn['device'],peers={public_key : {'allowed-ips' : [ipaddress.ip_network(new_ip+"/32")]}} )
    cur.execute(f"""insert or replace into conn  (usuario,pubkey,ip) VALUES ('{user}','{public_key}','{int(ipaddress.ip_address(new_ip))}')""")
    get_db().commit()
    get_db().close()
    return jsonify(response)        


@app.route('/status',methods=['GET', 'POST'])
def status():
    print(".")
if __name__ == '__main__':
    app.run(threaded=False, processes=1)