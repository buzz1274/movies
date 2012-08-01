#!/usr/bin/python2.7
import os
import re
import csv
import stat
import json
from fabric.api import settings, hide
from fabric.operations import get
from fabric.operations import run

film = []
months = {'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr': '04', 'May': '05',
         'Jun':'06', 'Jul':'07', 'Aug':'08', 'Sep': '09', 'Oct': '10',
         'Nov':'11', 'Dec':'12'}
output = ""
GB = 1024 * 1024 * 1024
with settings(hide('running', 'stdout'), warn_only=True,
              host_string='media.zz50.local',
              user='dave',
              password='letmein'):
    for path in ['movies', 'movies2']:
         output += run(("find %s -type f -print0 | xargs -0 ls -la" % path),
                      shell=False, pty=True, combine_stderr=True)
                                              
if output:
    for line in output.split("\r\n"):        
        movie = re.search('.*wheel\s+([0-9]{1,})\s(.*?)(movies.*?(20|19)[0-9]{2})(.*)', line)
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
        else:
            print line