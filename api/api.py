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

with open(r'api/config.yaml') as file:
    config = yaml.load(file, Loader=yaml.FullLoader)
config_ldap=config['ldap']
config_vpn=config['vpn']
print(config_vpn)

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
        reserved_ips=list()
        reserved_ips.extend(ipaddress.ip_network(config_vpn['node-ip']).hosts())
        for ip in config_vpn['reserved-ips'] :
            reserved_ips.extend(ipaddress.ip_network(ip).hosts())
        avaliable_ip=set(ipaddress.ip_network('10.0.0.0/24').hosts())-set(reserved_ips)
        new_ip=str(next(iter(avaliable_ip)))
        public_key, private_key = wgtools.keypair()
        psk = wgtools.genpsk()
        values = {
        "public_key": public_key,
        "private_key": private_key,
        "psk": psk,
        "ip": new_ip,
        "route_networks": config_vpn['routing-subnets']
        }
    return jsonify(values)        


@app.route('/status',methods=['GET', 'POST'])
def status():
    print(".")
app.run()
