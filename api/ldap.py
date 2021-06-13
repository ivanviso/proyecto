def ldaplogin(host,user,dn,psw):
    from ldap3 import Server, Connection, ALL
    s = Server(host, get_info=ALL) 
    c = Connection(s, user=f'cn={user},{dn}', password=psw)
    if not c.bind():
        print('error in bind', c.result)
ldaplogin ('167.99.221.214','usuario2111','ou=usuarios,dc=ldap,dc=ivan,dc=site','abcABC123'