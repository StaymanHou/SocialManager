from basic_posterhandler import *
import time
from time import sleep
from base64 import b64encode
import urllib
import twitter_requests
import re
from ..MyQueue import *
from ..RssPost import *
from ..Tags import *

class handler(basicposterhandler):

    def __init__(self):
        super(handler, self).__init__()
        self.module_name = 'twitter'

    # override
    def without_session(self, load_iteration=1):
        s = twitter_requests.Session()
        url = 'https://twitter.com/login'
        r = s.get(url)
        if r.status_code!=200:
            raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        # extract authenticity_token
        m = re.search('<input type="hidden" name="authenticity_token" value=".+?">',r.text)
        if m is None:
            raise Exception('login error: token not found.')
        auth_token = m.group(0)[54:-2]
        sleep(load_iteration)
        # login
        url = 'https://twitter.com/sessions'
        payload = {'session[username_or_email]': self.AccSet['USERNAME'],
                   'session[password]': self.AccSet['PSWD'],
                   'scribe_log': '',
                   'return_to_ssl': 'true',
                   'remember_me': '0',
                   'redirect_after_login:': '',
                   'authenticity_token': auth_token}
        r = s.post(url, data=payload)
        if r.status_code!=200:
            raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        self.session = {'s': s, 'auth_token': auth_token}
        sleep(load_iteration)
        self.with_session()

    # override
    def with_session(self, load_iteration=1):
        s = self.session['s']
        auth_token = self.session['auth_token']
        # type 1: text tweet
        if self.QI['TYPE']==1:
            if (self.QI['EXTRA_CONTENT'] is None or self.QI['EXTRA_CONTENT'].strip()==''):
                extra_content = ''
            else:
                extra_content = ' ' + self.QI['EXTRA_CONTENT'].strip()
            tweet_content = addhashtag(self.QI['TITLE'], self.QI['TAG'], mode = 1)
            if len(tweet_content)>=(117-len(extra_content)):
                tweet_content = tweet_content[:(113-len(extra_content))] + '... '
            url = 'https://twitter.com/i/tweet/create'
            payload = {'authenticity_token': auth_token,
                       'place_id': '',
                       'status': tweet_content.encode('ascii', 'ignore')+' '+self.QI['LINK']+extra_content.encode('ascii', 'ignore')}
            r = s.post(url, data=urllib.urlencode(payload))
            if r.status_code!=200:
                raise Exception('unexpected response: %s : %s'%(url, r.status_code))
            # type 2: tweet with image
        elif self.QI['TYPE']==2 and self.QI['IMAGE_LINK']:
            self.garentee_imgfile()
            imgfile = open(self.temp_img_dir+self.QI['IMAGE_FILE'], 'rb').read()
            if (self.QI['EXTRA_CONTENT'] is None or self.QI['EXTRA_CONTENT'].strip()==''):
                extra_content = ''
            else:
                extra_content = ' ' + self.QI['EXTRA_CONTENT'].strip()
            tweet_content = addhashtag(self.QI['TITLE'], self.QI['TAG'], mode = 1)
            if (self.QI['IMAGE_FILE'] is None) or (self.QI['IMAGE_FILE']==''):
                raise Exception('No image file specified in a image type tweet.')
            if len(tweet_content)>=(93-len(extra_content)):
                tweet_content = tweet_content[:(89-len(extra_content))] + '... '
            url = 'https://upload.twitter.com/i/tweet/create_with_media.iframe'
            payload = {'post_authenticity_token': auth_token,
                       'iframe_callback': 'window.top.swift_tweetbox_'+str(int(time.time()))+'000',
                       'in_reply_to_status_id': '',
                       'impression_id': '',
                       'earned': '',
                       'status': tweet_content.encode('ascii', 'ignore')+' '+self.QI['LINK']+extra_content.encode('ascii', 'ignore'),
                       'media_data[]': b64encode(imgfile),
                       'place_id': ''}
            files = {'media_empty': ('', '')}
            r = s.post(url, data=payload, files=files)
            if r.status_code!=200:
                raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        else:
            raise Exception('wrong type: %d'%self.QI['TYPE'])
        self.session['s'] = s
    
