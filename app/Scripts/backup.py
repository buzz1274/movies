import os
import sys
import datetime
from config import Config

try:
    config = Config()
except Exception, e:
    sys.exit("An Error Occurred %s " % e)

if not os.path.isdir(config.backup_path):
    print "Backup Path does not exist"
    sys.exit()

sql_dump_file = 'movies.sql'
os.popen('export PGPASSWORD="%s";pg_dump -U %s %s '
         '--inserts --clean > %s/%s' %
          (config.db_password, config.db_user,
           config.db_name, config.backup_path, sql_dump_file))

if not os.path.isfile(config.backup_path + '/' + sql_dump_file):
    print "Error Dumping DB"
    sys.exit()

image_archive_file = 'images.tar.gz'
os.popen('cd %s;tar -czf %s/%s *' %
         (config.image_save_path, config.backup_path, image_archive_file))

if not os.path.isfile(config.backup_path + '/images.tar.gz'):
    print "Error Archiving Movie Images"
    sys.exit()

backup_file = 'movies_%s.tar.gz' % (datetime.date.today())
os.popen('cd %s;tar -czf %s %s %s' %
         (config.backup_path, backup_file, sql_dump_file, image_archive_file))

os.popen('rm %s/%s' % (config.backup_path, sql_dump_file))
os.popen('rm %s/%s' % (config.backup_path, image_archive_file))

files = os.popen('ls -t %s/*.tar.gz' % (config.backup_path))

if files:
    files = list(files)[config.backup_files_to_keep:]
    if files:
        for file in files:
            os.popen('rm %s' % (file))