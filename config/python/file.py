#!/usr/bin/python2.7
import os
import re
import csv
import stat
import json
import sys
import ConfigParser
from fabric.api import settings, hide
from fabric.operations import get
from fabric.operations import run
from config import CONFIG_PATH

class File():
            
    hostname = None
    
    username = None
    
    password = None
    
    regex_pattern = None
    
    movies = ""
    
    def __init__(self):
        """
        File constructor reads ini file for configuration 
        settings
        """
        try:
            self._set_config_variables()
        except ConfigParser.Error, e:
            print 'Invalid config file(%s)' % e
                
    def spider(self):
        """
        spiders the supplied drives and directories for files 
        """
        with settings(hide('running', 'stdout'), warn_only=True,
                      host_string=self.hostname,
                      user=self.username,
                      password=self.password):
            for path in ['movies', 'movies2']:
                 self.movies += \
                    run(("find %s -type f -print0 | xargs -0 ls -la" % path),
                        shell=False, pty=True, combine_stderr=True)
                    
        if self.movies:
            self._parse()
            
    def _set_config_variables(self):
        """
        retrieves and parse the appropriate variables from
        the config file 
        """        
        config = ConfigParser.ConfigParser()
        config.read(CONFIG_PATH)
        self.hostname = config.get('MEDIASERVER', 'hostname')
        self.username = config.get('MEDIASERVER', 'username')
        self.password = config.get('MEDIASERVER', 'password')
        self.regex_pattern = config.get('MEDIASERVER', 'regex_pattern')            
                    
    def _parse(self):
        """
        parses the list of files generated whilst spidering into a list
        of valid media files
        """
        film = []
        
        for line in self.movies.split("\r\n"):  
            movie = re.search(self.regex_pattern, line)
            print line
            print movie
            print movie.group(3)
                 
"""     
        film = []
        months = {'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr': '04', 'May': '05',
                  'Jun':'06', 'Jul':'07', 'Aug':'08', 'Sep': '09', 'Oct': '10',
                  'Nov':'11', 'Dec':'12'}
        GB = 1024 * 1024 * 1024                                                 
        if output:
            for line in output.split("\r\n"):        
                movie = re.search(self.regex_pattern, line)
                if (movie and movie.group(1) and movie.group(2) and movie.group(3) and 
                    movie.group(4)):
                    
                    date = re.split('\s+', movie.group(2).strip())
                    
                    if not date or len(date) != 3:
                        #date is today
                        print "today"
                    else:
                        try:
                            archive_date = str(int(date[2])) + "-"
                        except ValueError:
                            archive_date = '2012-' #this year
                            
                        if int(date[1])  < 10:
                            date[1] = "0" + str(date[1])
                                                
                        archive_date += months[date[0]] + "-" + date[1]
                                                               
                    path = movie.group(3).strip() + movie.group(5)
                    title = re.search('.*\/(.*?)\[', path)
                    year = re.search('\[([0-9]{4})\]', path)
                    size = str(round((float(movie.group(1)) / GB), 2))          
                                
                    if title and title.group(1) and size and year and year.group(1):
                        if re.search('\[HD\]', path):
                            hd = 'Y'
                        else:
                            hd = 'N'
                                                                        
                        film.append({'title': title.group(1), 
                                     'year': year.group(1), 
                                     'size': size, 
                                     'hd': hd,
                                     'archive_date': archive_date,
                                     'path': path})
                    
                        film = sorted(film, key=lambda k: k['title'])
                        file = open('movies.json', 'w')
                        file.write(json.dumps(film))
                        file.close()
"""                                                              