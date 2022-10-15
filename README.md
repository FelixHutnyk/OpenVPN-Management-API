# OpenVPN-Management-API

OpenVPN-Management-API is a simple php api written with slim to query an OpenVPN Servers Management daemon.

## API

### /v1/connections
* type: GET
* return: json

### /v1/disconnect
* type: POST
* parameters: 'username' - Client username you wish to disconnect.
