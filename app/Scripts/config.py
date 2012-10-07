#!/usr/bin/python2.7
import re
import os
import ConfigParser
import sys
from sqlalchemy import create_engine, Table, MetaData, exc

class Config(object):

    CONFIG_PATH = re.sub('Scripts.*', 'Config/',
                         os.path.dirname(os.path.abspath(__file__))) + 'config.ini'

    hostname = None

    username = None

    password = None

    regex_pattern = None

    image_save_path = None

    movie_table = None

    genre_table = None

    movie_genre_table = None

    role_table = None

    person_table = None

    movie_role_table = None

    db_engine = None

    db_user = None

    db_host = None

    db_port = None

    db_name = None

    db = None

    backup_path = None

    backup_files_to_keep = None

    def __init__(self):
        """
        """
        try:
            self._set_config_variables()
            self.db = create_engine('%s://%s:%s@%s:%s/%s' %
                                     (self.db_engine, self.db_user,
                                      self.db_password, self.db_host,
                                      self.db_port, self.db_name))
            self.movie_table = Table('movie', MetaData(), autoload=True,
                                     autoload_with=self.db)
            self.genre_table = Table('genre', MetaData(), autoload=True,
                                     autoload_with=self.db)
            self.movie_genre_table = Table('movie_genre', MetaData(),
                                           autoload=True,
                                           autoload_with=self.db)
            self.role_table = Table('role', MetaData(), autoload=True,
                                    autoload_with=self.db)
            self.person_table = Table('person', MetaData(), autoload=True,
                                      autoload_with=self.db)
            self.movie_role_table = Table('movie_role', MetaData(),
                                          autoload=True, autoload_with=self.db)

        except ConfigParser.Error, e:
            sys.exit('Invalid config file(%s)' % e.message)
        except exc.OperationalError, e:
            sys.exit('Database Error(%s)' % e.message)

    def _set_config_variables(self):
        """
        retrieves and parse the appropriate variables from
        the config file
        """
        config = ConfigParser.ConfigParser()
        config.read(self.CONFIG_PATH)
        self.hostname = config.get('MEDIASERVER', 'hostname').strip('"')
        self.username = config.get('MEDIASERVER', 'username').strip('"')
        self.password = config.get('MEDIASERVER', 'password').strip('"')
        self.regex_pattern = config.get('MEDIASERVER', 'regex_pattern').strip('"')
        self.image_save_path = config.get('WEBESERVER', 'image_save_path').strip('"')
        self.db_engine = config.get('DB', 'db_engine').strip('"')
        self.db_user = config.get('DB', 'db_user').strip('"')
        self.db_password = config.get('DB', 'db_password').strip('"')
        self.db_host = config.get('DB', 'db_host').strip('"')
        self.db_port = config.get('DB', 'db_port').strip('"')
        self.db_name = config.get('DB', 'db_name').strip('"')
        self.backup_path = config.get('BACKUP', 'backup_path').strip('"')
        self.backup_files_to_keep = int(config.get('BACKUP', 'backup_files_to_keep').strip('"'))