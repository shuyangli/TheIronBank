require 'net/http'
require 'uri'
require 'thread'
require 'json'

file_content = []

NUM_OF_THREADS = 40
mutex = Mutex.new

# Read file
File.open("imdb_ids").each do |line|
	file_content << line
end

# Write file
director_file = File.open("director_data", "w")
writer_file = File.open("writer_data", "w")

# Casually parallelize networking
NUM_OF_THREADS.times.map {

	Thread.new(file_content) { |file_content|

		while imdb_id = mutex.synchronize { file_content.pop }

			# Form the URI
			imdb_id.strip!
			uri = URI.parse(URI.escape "http://www.imdbapi.com/?i=#{imdb_id}&plot=short&r=json")

			# Request
			response = Net::HTTP.get_response(uri)
			response_json_string = response.body

			begin
				# This gives us a JSON::ParserError with improperly escaped JSON response
				response_json = JSON.parse(response.body)
			rescue JSON::ParserError
				next
			end

			# If the film doesn't exist, skip;
			# otherwise add it
			if (response_json["Response"] == "True") then

				new_film = {}
				new_film[:director] = if response_json["Director"] then response_json["Director"].split(',').map{ |name| name.strip } end
				new_film[:writer] = if response_json["Writer"] then response_json["Writer"].split(',').map{ |name| name.strip } end
				new_film[:writer].each do |name|
					name.gsub!(/\(.*\)/, "")
				end

				# Print out stuff
				mutex.synchronize {
					new_film[:director].each do |director|
						director_file.puts("#{imdb_id}|#{director}")
					end

					new_film[:writer].each do |writer|
						writer_file.puts("#{imdb_id}|#{writer}")
					end
				}

			end
		end

	}

}.each(&:join)

director_file.close
writer_file.close
