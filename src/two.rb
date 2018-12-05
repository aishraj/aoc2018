fs = File.read("src/two_input.txt")
contents = fs.split()
p "contents size is #{contents.size}"
current_index = 0
res = [0]
while (true) 
	puts "res is #{res}"
	s = res[-1] + (contents[current_index]).to_i
	if res.include?(s)
		puts "your result is #{s}"
		break
	end
	res << s
	current_index = ((current_index + 1) % contents.size)
end