var ws = {
    "url": "http://localhost:8888/webSPELL-4.2.3", // no trailing slash
    "viewport": {
        "width": 1024,
        "height": 768
    },
    "user": {
        "admin": {
            "id": "admin",
            "password": "admin"
        },
        "customer": {
            "id": "user",
            "password": "user"
        }
    },
    "routine": {},
    "elements": {
        "loginForm": "form[name=login]"
    }
};

casper.options.viewportSize = {
    width: ws.viewport.width,
    height: ws.viewport.height
};
