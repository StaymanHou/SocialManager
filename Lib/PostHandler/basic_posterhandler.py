import os
import requests
from ..MyFunction import randomString
from ..systemHelper import specialize_path
import time

class basicposterhandler(object):

    def __init__(self):
        super(basicposterhandler, self).__init__()
        self.module_name = 'basic_module'
        self.session = None
        self.temp_img_dir = ''

    def handle(self, sessions, task, temp_img_dir):
        self.temp_img_dir = temp_img_dir
        self.Mod = task['Mod']
        self.Acc = task['Acc']
        self.AccSet = task['self.AccSet']
        self.QI = task['QI']
        if self.Mod['NAME'] in sessions and self.Acc['NAME'] in sessions[self.Mod['NAME']] and sessions[self.Mod['NAME']][self.Acc['NAME']] is not None:
            self.session = sessions[self.Mod['NAME']][self.Acc['NAME']]
            try:
                self.with_session()
            except Exception, e:
                raise e
            finally:
                if self.QI['IMAGE_FILE']:
                    imgflpath = self.temp_img_dir+self.QI['IMAGE_FILE']
                    try: os.remove(imgflpath)
                    except: pass
        else:
            try:
                self.without_session()
            except Exception, e:
                raise e
            finally:
                if self.QI['IMAGE_FILE']:
                    imgflpath = self.temp_img_dir+self.QI['IMAGE_FILE']
                    try: os.remove(imgflpath)
                    except: pass
        if self.Mod['NAME'] not in sessions:
            sessions[self.Mod['NAME']] = {}
        sessions[self.Mod['NAME']][self.Acc['NAME']] = self.session

    def without_session(self, load_iteration=1):
        raise Exception('posterhanler#without_session is working! Please override the method to make it "really" working!')

    def with_session(self, load_iteration=1):
        raise Exception('posterhanler#with_session is working! Please override the method to make it "really" working!')

    def garentee_imgfile(self):
        r = requests.get(self.QI['IMAGE_LINK'])
        if r.status_code != 200:
            raise Exception('unexpected response when download image')
        self.QI['IMAGE_FILE'] = str(time.time())+'_'+randomString(16)+'.'+self.QI['IMAGE_LINK'].split('.')[-1].strip()
        with open(specialize_path(self.temp_img_dir+self.QI['IMAGE_FILE']), 'wb') as f:
            for chunk in r.iter_content():
                f.write(chunk)

