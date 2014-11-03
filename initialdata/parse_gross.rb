require 'rubygems'
require 'nokogiri'

Dir.chdir("gross_dir")

`ls .`.split("\n").each do |filename|

	html_line = IO.readlines(filename)[140]
	5.times do
		html_line.chop!
	end

	# Reading the doc
	doc = Nokogiri::HTML(html_line)
	doc = Nokogiri::HTML("#{doc.xpath("//table")[1]}")

	# For everything except the header
	doc.xpath("//tr[position() > 1]").each do |row|

		# All actual movies
		row = Nokogiri::HTML("#{row.xpath("*[position() < last()]")}")

		movie = row.xpath("//b").text
		distributor = row.xpath("//font")[1].text
		gross = row.xpath("//font")[2].text

		# Sanitize data
		if gross == "n/a" then
			gross = "-1";
		else
			grossArr = gross.split ","
			grossArr[0].slice!(1, grossArr[0].length)
			gross = grossArr.join ""
		end

		puts "#{movie}/#{distributor}/#{gross}"
	end
end
