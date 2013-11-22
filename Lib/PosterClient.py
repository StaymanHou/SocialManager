from twisted.internet.protocol import ClientFactory, Protocol
from twisted.internet.task import LoopingCall
from twisted.internet import reactor
import pickle
import logging

class PosterClient(Protocol):
    def connectionMade(self):
        msg = {'status': 'join', 'body': None}
        self.sendObj(msg)
        self.factory.clientConnectionMade(self)

    def sendObj(self, ob):
        s = pickle.dumps(ob)
        self.transport.write(s)

    def dataReceived(self, data):
        msg = pickle.loads(data)
        if msg['response'] == 'task':
            with self.factory.queueLock:
                self.factory.workQueue.put(msg['body'])
        else:
            logging.info('Server says: '+str(msg))

class PosterClientFactory(ClientFactory):
    protocol = PosterClient

    def __init__(self, taskhandlethread, workQueue, queueLock):
        self.taskhandlethread = taskhandlethread
        self.workQueue = workQueue
        self.queueLock = queueLock
        self.client = None
        self.lc = LoopingCall(self.clientReport)
        self.lc.start(10)

    def clientReport(self):
        if self.client is None:
            return
        status = self.taskhandlethread.status
        msg = {'status': status}
        if status == 'idle':
            msg['body'] = None
        elif status == 'working':
            msg['body'] = None
        elif status == 'finish':
            with self.queueLock:
                msg['body'] = self.workQueue.get()
            self.taskhandlethread.status = 'idle'
        else:
            raise Exception('unknown status')
        self.client.sendObj(msg)

    def clientConnectionMade(self, client):
        self.client = client

    def clientConnectionFailed(self, connector, reason):
        print "Connection failed - goodbye!"
        self.client = None
        reactor.stop()

    def clientConnectionLost(self, connector, reason):
        print "Connection lost - goodbye!"
        self.client = None
        reactor.stop()
