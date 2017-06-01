[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eduvpn/vpn-token-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eduvpn/vpn-token-service/?branch=master)

VPN Token Service.

This service is used to provide an access token to interact with the API of VPN 
services in the "federated" model.

It will authenticate the user, using a SAML identity provider, generate an
access token signed using public key cryptography.
