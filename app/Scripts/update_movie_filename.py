#!/usr/bin/python
"""
loops through all movie files allowing the imdb_id to be added to the filename
"""
from movie import Movie
import re

movie = Movie()
lines = movie.scan_folders()

if lines:
    for line in lines.split("\r\n"):
        line = re.search(movie.config.regex_pattern, line)
        if (line and line.group(1) and line.group(2) and
            line.group(3) and line.group(4) and
            line.group(5) and re.match("[0-9]", line.group(5))):

            title = line.group(4)
            old_path = line.group(2)

            imdb_id = raw_input("%s:" % (title))
            new_path = re.sub('\[.*\]', '[' + imdb_id + ']', old_path)

            movie.rename_movie(old_path, new_path)

