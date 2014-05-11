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
    movies_missing_images = movie.find_missing_images()

    if invalid_movies or movies_missing_images:
        body = ""
        if invalid_movies:
            body = "Movies with Incomplete Data(%d)\n" % (len(invalid_movies))
            for invalid_movie in invalid_movies:
                body += "%s %s %s\n" % (invalid_movie.movie_id,
                                        invalid_movie.imdb_id,
                                        invalid_movie.title)
            body += "\n"
        if movies_missing_images:
            body += "Movies with Missing Image(%d)\n" % (len(movies_missing_images))
            for movies_missing_image in movies_missing_images:
                body += "%s %s %s\n" % (movies_missing_image.movie_id,
                                        movies_missing_image.imdb_id,
                                        movies_missing_image)

        message = MIMEText(body)
        message['Subject'] = 'Movie Spidering Issues'
        message['From'] = config.email_address
        message['To'] = config.email_address

        mail = smtplib.SMTP('localhost')
        mail.sendmail(config.email_address, [config.email_address],
                      message.as_string())
        mail.quit()
except Exception, e:
    print e, type(e)
    pass