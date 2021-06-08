from hashlib import new
import flask
from flask import request, jsonify
import json
from ldap3.protocol.convert import substring_to_dict
from pyasn1.type.univ import Null
import yaml
import ipaddress
import wgtools
import sqlite3

con = sqlite3.connect('wgapi.db')

def loadconfig():
    with open(r'api/config.yaml') as file:
        config = yaml.load(file, Loader=yaml.FullLoader)
    global config_ldap,config_vpn
    config_ldap=config['ldap']
    config_vpn=config['vpn']



def ldaplogin(host,user,dn,psw):
    from ldap3 import Server, Connection, ALL
    s = Server(host, get_info=ALL) 
    c = Connection(s, user=f'cn={user},{dn}', password=psw)
    if not c.bind():
        return c.result



app = flask.Flask(__name__)
app.config["DEBUG"] = True


@app.route('/', methods=['GET', 'POST'])
def home():
    if request.method == 'POST':
        return request.args
    if request.method == 'GET':
        return "as"


@app.route('/login',methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        loadconfig()
        new_ip=None
        reserved_ips=list()
        for ip in config_vpn['reserved-ips'] :
            reserved_ips.extend(ipaddress.ip_network(ip).hosts())
        avaliable_ip=set(ipaddress.ip_network('10.0.0.0/8').hosts())-set(reserved_ips)
        new_ip=str(next(iter(avaliable_ip)))
        public_key, private_key = wgtools.keypair()
        psk = wgtools.genpsk()
        reserved_ips.append(ipaddress.ip_address(new_ip))
        print(reserved_ips)
        values = {
        "public_key": public_key,
        "private_key": private_key,
        "psk": psk,
        "ip": new_ip,
        "route_networks": config_vpn['routing-subnets'],
        }
    return jsonify(values)        


@app.route('/status',methods=['GET', 'POST'])
def status():
    print(".")
app.run()
