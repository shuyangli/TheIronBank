require 'net/http'
require 'uri'
require 'thread'
require 'json'

imdb_arr = []

NUM_OF_THREADS = 40
mutex = Mutex.new

# Read file
File.open("imdb_data").each do |line|
	imdb_arr << line.split('|')[0]
end

# Opening files for writing
actor_file = open("actor_data", 'w')
genre_file = open("genre_data", 'w')

# Casually parallelize networking
NUM_OF_THREADS.times.map {

	Thread.new(imdb_arr) { |imdb_arr|

		while imdb_id = mutex.synchronize { imdb_arr.pop }

			# Form the URI
			uri = URI.parse(URI.escape "http://www.omdbapi.com/?i=#{imdb_id}&plot=short&r=json")

			# Request
			response = Net::HTTP.get_response(uri)
			response_json_string = response.body

			begin
				# This gives us a JSON::ParserError with improperly escaped JSON response
				response_json = JSON.parse(response.body)
			rescue JSON::ParserError
				next
			end

			# Write actor data
			actors = response_json["Actors"].split(',').map{ |x| x.strip }
			actors.each do |actor|
				mutex.synchronize { actor_file.puts "#{response_json["imdbID"]}|#{actor}" }
			end

			# Write genre data
			genres = response_json["Genre"].split(',').map{ |x| x.strip }
			genres.each do |genre|
				mutex.synchronize { genre_file.puts "#{response_json["imdbID"]}|#{genre}" }
			end

		end

	}

}.each(&:join)
