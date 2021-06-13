j=2
for i in range (1002,11000) :
    j=j+1
    print(f"""
dn: cn=usuario{j},ou=usuarios,dc=ldap,dc=ivan,dc=site
objectClass: top
objectClass: account
objectClass: posixAccount
objectClass: shadowAccount
userPassword : abcABC123
cn: usuario{j}
uid: usuario{j}
uidNumber: {i}
gidNumber: {i}
homeDirectory : /home/usuario
"""
)