require 'net/http'
require 'uri'
require 'thread'
require 'json'

class AwardParser

	def initialize
		@current_state = "0"
		@total_awards = 0
	end

	def read_char(c)

		if c =~ /[0-9]/ then
			if @current_state == "0" then
				@current_state = c
			else
				@current_state << c
			end
		else
			# c is a character, we parse the current_state
			# and add to total_awards
			current_awards = Integer(@current_state)
			@total_awards += current_awards
			@current_state = "0"
		end

	end

	def total_awards
		return @total_awards
	end

end

file_content = []
returned_content = []

NUM_OF_THREADS = 12
threads = []
mutex = Mutex.new

# Read file
File.open("gross_data_sample").each do |line|
	file_content << line
end

# Casually parallelize networking

NUM_OF_THREADS.times.map {

	Thread.new(file_content, returned_content) { |file_content, returned_content|

		while line = mutex.synchronize { file_content.pop }

			# Form the URI
			line_array = line.split "|"
			line_array[2].strip!
			uri = URI.parse(URI.escape "http://www.imdbapi.com/?t=#{line_array[0]}")

			# Request
			response = Net::HTTP.get_response(uri)
			response_json = JSON.parse(response.body)

			# If the film doesn't exist, skip;
			# otherwise add it
			if (response_json["Response"] == "True") then

				new_film = {}
				new_film["IMDB_ID"] = if response_json["imdbID"] then response_json["imdbID"] end

				# Sanity check IMDB_ID: if it doesn't exist, we don't allow it
				if !new_film["IMDB_ID"] then
					next
				end

				# Keep getting other data
				new_film["Poster_URL"] = if response_json["Poster"] then response_json["Poster"] else "\\N" end
				if new_film["Poster_URL"] == "N/A" then new_film["Poster_URL"] = "\\N" end

				new_film["Description"] = if response_json["Plot"] then response_json["Plot"].strip else "\\N" end
				if new_film["Description"] == "N/A" then new_film["Description"] = "\\N" end

				# Sanitize runtime to NULL if it's N/A
				new_film["Runtime_Min"] = if response_json["Runtime"] then response_json["Runtime"].chomp " min" else "\\N" end
				if new_film["Runtime_Min"] == "N/A" then new_film["Runtime_Min"] = "\\N" end

				# MPAA_Rating is either a valid rating or NULL
				new_film["MPAA_Rating"] = if response_json["Rated"] then response_json["Rated"] else "\\N" end
				if new_film["MPAA_Rating"] == "N/A" then new_film["MPAA_Rating"] = "\\N" end

				new_film["Gross"] = if line_array[2] != "-1" then line_array[2].chomp "*" else "\\N" end
				new_film["Release_Year"] = if response_json["Year"] then response_json["Year"].chomp "-" else "\\N" end

				# Parse number of awards
				if response_json["Awards"] then
					num_awards_str = response_json["Awards"]
					parser = AwardParser.new
					num_awards_str.each_char do |c|
						parser.read_char c
					end

					new_film["Num_Awards"] = parser.total_awards
				else
					new_film["Num_Awards"] = "0"
				end

				new_film["Title"] = if response_json["Title"] then response_json["Title"] else "\\N" end
				new_film["Distributor"] = line_array[1]

				# Print out stuff
				mutex.synchronize { returned_content << "#{new_film["IMDB_ID"]}|#{new_film["Poster_URL"]}|#{new_film["Description"]}|#{new_film["Runtime_Min"]}|#{new_film["MPAA_Rating"]}|#{new_film["Gross"]}|#{new_film["Release_Year"]}|#{new_film["Num_Awards"]}|#{new_film["Title"]}|#{new_film["Distributor"]}" }

			end
		end

	}

}.each(&:join)

returned_content.each do |item|
	puts item
end
