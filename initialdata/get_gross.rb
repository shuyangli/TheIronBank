require 'net/http'

Dir.chdir("gross_dir")

{
	"NUM" => 1, "A" => 8, "B" => 8, "C" => 7, "D" => 6, "E" => 6, "F" => 6, "G" => 7, "H" => 6, "I" => 6, "J" => 4, "K" => 4, "L" => 5, "M" => 6, "N" => 5, "O" => 5, "P" => 7, "Q" => 1, "R" => 6, "S" => 13, "T" => 8, "U" => 2, "V" => 3, "W" => 6, "X" => 1, "Y" => 2, "Z" => 1 
}.each_pair do | letter, numpages |

	i = 1

	while i <= numpages do
		puts "#{letter}#{i}"

		# Get result and write to file
		httpstring = Net::HTTP.get(URI("http://www.boxofficemojo.com/movies/alphabetical.htm?letter=#{letter}&page=#{i}&p=.htm"))
		File.open("#{letter}#{i}.html", mode = 'w') do |file|
			file.write(httpstring)
		end

		i = i + 1
	end
end