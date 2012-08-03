#!/usr/bin/python2.7
import re
import os

CONFIG_PATH = re.sub('config.*', 'config/', 
                     os.path.dirname(os.path.abspath(__file__))) + 'config.ini'