#!/usr/bin/python
import smtplib
from config import Config
from email.mime.text import MIMEText
from movie import Movie

config = Config()
movie = Movie()

movie.update_rating()
movie.update_movies()
movie.update_invalid_movies()

try:
    invalid_movies = movie.find_invalid_movies()
    if invalid_movies:
        body = "Total Movies(%d)\n" % (len(invalid_movies))
        for movie in invalid_movies:
            body += "%s %s %s\n" % (movie.movie_id, movie.imdb_id, movie.title)

        message = MIMEText(body)
        message['Subject'] = 'Movies with Incomplete Data'
        message['From'] = config.email_address
        message['To'] = config.email_address

        mail = smtplib.SMTP('localhost')
        mail.sendmail(config.email_address, [config.email_address],
                      message.as_string())
        mail.quit()
except Exception, e:
    pass
