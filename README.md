# whmcs-internetx-registrar
The InterNetX WHMCS Registrar Module


### Requirements (MacOS):
 - gnu-sed (`brew install gnu-sed`)


### Development mode/Environment Variables:
Primarily for development purpose following variables may be used to overwrite default behavior.
None of these are guaranteed to remain stable or work in production.
```dotenv
# Use insecure curls and ignore invalid or unchecked ssl certificates
DOMAINROBOT_INSECURE=true
# Configure DNS provider nameservers. Used to detect if zones are created and can be set to demo or development api server roots
DNS_NS_ROOT=demo.autodns2.de
```
