<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $certificate = array(
		'certificate_id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'certificate' => array('type' => 'string', 'null' => false, 'length' => 3),
		'order' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'certificate_id')
		),
		'tableParameters' => array()
	);

	public $genre = array(
		'genre_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'genre' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'genre_id'),
			'genre_genre_key' => array('unique' => true, 'column' => 'genre')
		),
		'tableParameters' => array()
	);

	public $keyword = array(
		'keyword_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'keyword' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'keyword_id'),
			'keyword_idx' => array('unique' => true, 'column' => 'keyword')
		),
		'tableParameters' => array()
	);

	public $media = array(
		'media_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'media_format_id' => array('type' => 'integer', 'null' => false),
		'media_region_id' => array('type' => 'integer', 'null' => false),
		'media_storage_id' => array('type' => 'integer', 'null' => false),
		'amazon_asin' => array('type' => 'string', 'null' => false, 'length' => 10),
		'purchase_price' => array('type' => 'float', 'null' => true),
		'current_price' => array('type' => 'float', 'null' => true),
		'special_edition' => array('type' => 'boolean', 'null' => false),
		'boxset' => array('type' => 'boolean', 'null' => false),
		'notes' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'date_price_last_updated' => array('type' => 'date', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'media_id')
		),
		'tableParameters' => array()
	);

	public $media_format = array(
		'media_format_id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'media_format' => array('type' => 'string', 'null' => false, 'length' => 7),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'media_format_id')
		),
		'tableParameters' => array()
	);

	public $media_region = array(
		'media_region_id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'region' => array('type' => 'string', 'null' => false, 'length' => 4),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'media_region_id')
		),
		'tableParameters' => array()
	);

	public $media_storage = array(
		'media_storage_id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'media_storage' => array('type' => 'string', 'null' => false, 'length' => 6),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'media_storage_id')
		),
		'tableParameters' => array()
	);

	public $movie = array(
		'movie_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'imdb_id' => array('type' => 'string', 'null' => false, 'length' => 10),
		'title' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'path' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'filesize' => array('type' => 'biginteger', 'null' => false),
		'deleted' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'date_added' => array('type' => 'date', 'null' => false, 'default' => 'now()'),
		'date_last_scanned' => array('type' => 'date', 'null' => false, 'default' => 'now()'),
		'date_last_scraped' => array('type' => 'date', 'null' => true),
		'imdb_rating' => array('type' => 'float', 'null' => true),
		'runtime' => array('type' => 'integer', 'null' => true),
		'synopsis' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'release_year' => array('type' => 'integer', 'null' => true),
		'has_image' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'hd' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'certificate_id' => array('type' => 'integer', 'null' => true),
		'media_id' => array('type' => 'integer', 'null' => true),
		'width' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'height' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'movie_id'),
			'imdb' => array('unique' => true, 'column' => 'imdb_id'),
			'certificate_idx' => array('unique' => false, 'column' => 'certificate_id')
		),
		'tableParameters' => array()
	);

	public $movie_genre = array(
		'movie_id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
		'genre_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'mg' => array('unique' => true, 'column' => array('movie_id', 'genre_id'))
		),
		'tableParameters' => array()
	);

	public $movie_keyword = array(
		'movie_id' => array('type' => 'integer', 'null' => false),
		'keyword_id' => array('type' => 'integer', 'null' => false),
		'order' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'keyword_movie_idx' => array('unique' => true, 'column' => array('movie_id', 'keyword_id')),
			'mk' => array('unique' => true, 'column' => array('movie_id', 'keyword_id'))
		),
		'tableParameters' => array()
	);

	public $movie_role = array(
		'movie_id' => array('type' => 'biginteger', 'null' => false),
		'role_id' => array('type' => 'integer', 'null' => false),
		'person_id' => array('type' => 'biginteger', 'null' => false),
		'order' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'movie_role_movie_id_key' => array('unique' => true, 'column' => array('movie_id', 'role_id', 'person_id')),
			'movie_role_person_idx' => array('unique' => true, 'column' => array('movie_id', 'role_id', 'person_id'))
		),
		'tableParameters' => array()
	);

	public $person = array(
		'person_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'person_name' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'person_imdb_id' => array('type' => 'string', 'null' => true, 'length' => 10),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'person_id'),
			'person_idx' => array('unique' => false, 'column' => 'person_name')
		),
		'tableParameters' => array()
	);

	public $user = array(
		'user_id' => array('type' => 'integer', 'null' => false),
		'username' => array('type' => 'string', 'null' => false, 'length' => 50),
		'password' => array('type' => 'string', 'null' => false, 'length' => 50),
		'admin' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'date_added' => array('type' => 'date', 'null' => false),
		'name' => array('type' => 'string', 'null' => false, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);

	public $user_movie_downloaded = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false),
		'movie_id' => array('type' => 'integer', 'null' => false),
		'date_downloaded' => array('type' => 'datetime', 'null' => false),
		'filesize' => array('type' => 'float', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

	public $user_movie_favourite = array(
		'user_id' => array('type' => 'integer', 'null' => false),
		'movie_id' => array('type' => 'integer', 'null' => false),
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_movie_favourite_user_id_movie_id_idx' => array('unique' => true, 'column' => array('user_id', 'movie_id')),
			'user_movie_favourite_user_id_movie_id_key' => array('unique' => true, 'column' => array('user_id', 'movie_id'))
		),
		'tableParameters' => array()
	);

	public $user_movie_watched = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false),
		'movie_id' => array('type' => 'integer', 'null' => false),
		'date_watched' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

}
