from twisted.internet import ssl, reactor
from twisted.internet.protocol import Factory, Protocol

class Echo(Protocol):

    def __init__(self, bots):
        self.bots = bots

    def connectionMade(self):
        self.bots[0] += 1
        self.id = self.bots[0]
        self.bots[self.id] = self
    	print 'worker#%d conn'%self.id
        self.transport.write(
            "Welcome! There are currently %d open connections.\n" %
            (len(self.bots)-1,))

    def connectionLost(self, reason):
    	print 'worker#%d lost'%self.id
    	del self.bots[self.id]

    def dataReceived(self, data):
        self.transport.write('worker#'+str(self.id)+': '+data)

class EchoFactory(Factory):

    def __init__(self):
        self.bots = {0:0} # maps user names to Echo instances

    def buildProtocol(self, addr):
        return Echo(self.bots)


if __name__ == '__main__':
    reactor.listenSSL(8123, EchoFactory(),
                      ssl.DefaultOpenSSLContextFactory(
            'keys/server.key', 'keys/server.crt'))
    reactor.run()