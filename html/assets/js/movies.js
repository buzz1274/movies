var movies = function() {
	
	//movies returned from json call
	var movies = null;
	
	//filtered movies
	var filteredMovies = null;
	
	//total movies in current resultset
	var totalMovies = 0;
	
	//current page of results
	var page = 1;
	
	//number of results to show per page
	var resultsPerPage = 20;
	
	//last field to be sorted
	var sortField = null;
	
	//was the sort field reversed
	var sortReverse = null;
		
	//display movies
	var display = function() {
		count = 1;	
		displayCount();	
		$('#movies').html('');
		$.each(filteredMovies, function(key, movie) {
			if(count < page * resultsPerPage && 
			   count >= ((page - 1) * resultsPerPage) + 1) {
			   	if(movie.hd == 'Y') {
			   		hd_image = 'ticked.png';
			   	} else {
			   		hd_image = 'cross.png';
			   	}
				$('#movies').append(
					"<tr onmouseover='$(this).css(\"background-color\",\"#BADA55\");'"+
					"    onmouseout='$(this).css(\"background-color\",\"\");'>"+
					"<td><a href='http://www.imdb.com/find?q="+movie.title+"' target='_blank'>"+movie.title+"</a></td>"+
					"<td class='centre'>"+movie.year+"</td>"+
					"<td><img src='/assets/image/"+hd_image+"' width='20' height='20'></td>"+
					"<td>"+movie.size+"</td>"+
					"<td>"+movie.archive_date+"</td>"+
					"<td>Y:/"+movie.path+"</td>"+
					"<td><a href='javascript:void(0);' onclick='movies.displayInfo();'>"+
					"<img src='/assets/image/magnifying.png' width='20' height='20'>"+
					"</a></td></tr>"+
					"<tr style='display:none;'><td colspan='7'>&nbsp;</td></tr>");		
			}
			count++;							
		});				
	}
	//end display
		
	//displays movie count
	var displayCount = function() {
		$(".total_movies").html(filteredMovies.length);		
		$('.start_movies').html(((page - 1) * resultsPerPage) + 1);
		if(page == Math.ceil(totalMovies / resultsPerPage)) {
			$('.end_movies').html(totalMovies);
		} else {
			$('.end_movies').html(page  * resultsPerPage);
		}
	}
	//end displayCount
	
	//sort_by
	var sort_by = function(field, reverse, primer) {
   		var key = function (filteredMovies) {
   			return primer ? primer(filteredMovies[field]) : filteredMovies[field]
		};
   		return function (a,b) {
       		var A = key(a), B = key(b);
       		return ((A < B) ? -1 : (A > B) ? +1 : 0) * [-1,1][+!!reverse];                  
   		}   	
	}
	//end sort_by
	
	//display info
	this.displayInfo = function() {
		
		
		
	}	
	//end displayInfo
	
	//gets movies
	this.get = function() {
		$.getJSON("movies.json", function(data) {
			totalMovies = data.length;
			if(data && data.length > 0) {
				movies = data;
				filteredMovies = data;
				filteredMovies.sort(sort_by('archive_date', false, false));
				display();
			} else {
				alert("NO RESULTS");
			}		
		});			
	}
	//end get
	
	//display next page of results
	this.next = function() {
		if(page < Math.ceil(totalMovies / resultsPerPage)) {
			page++;
			display();
		}			
	}
	//end next
	
	//display next page of results
	this.prev = function() {
		if(page > 1) {
			page--;
			display();
		}			
	}		
	//end prev
	
	//filter movie results
	this.filter = function() {
		
	}
	//end filter		
	
	this.sort_movies = function(field, reverse, primer) {
		if(sortField == field) {
			reverse = !sortReverse;
		}
		sortField = field;
		sortReverse = reverse;
		filteredMovies.sort(sort_by(field, reverse, primer));
		page=1;
		display();		
	}
	//end sort
}

$(document).ready(function() {	  
	movies = new movies();		
	movies.get();
});