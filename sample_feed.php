<?php 

/*
    Copyright (C) 2011  Jason Hendriks

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// attempt to talk to Wordpress
$blog_header_path = preg_replace("/wp-content\/.*/", "wp-blog-header.php", getcwd());
$blog_header_path = "/Users/jason/Sites/wordpress/wp-blog-header.php";
if (file_exists($blog_header_path)) {
	require_once($blog_header_path);

	echo '<?xml version="1.0" encoding="utf-8"?>';?>

<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
	<title>Blip Slideshow Samples Feed</title>
	<description>This is the sample feed that comes bundled with Blip Slideshow</description>
	<link>http://www.jasonhendriks.com/programmer/blip-slideshow/</link>
	<pubDate><?php echo date('r') ?></pubDate>
	<item>
		<title>1</title>
		<description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Island Mountain&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/1.jpg', __FILE__) ?>" title="Island Mountain"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?>" width="720" height="540" alt="Island Mountain" title="Island Mountain" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
		<link><?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?></link>
		<guid>1</guid>
		<media:content url="<?php echo plugins_url('Slideshow/images/1.jpg', __FILE__) ?>" fileSize="85395" type="image/jpeg" medium="image" width="720" height="540"/>
		<media:thumbnail url="<?php echo plugins_url('Slideshow/images/1t.jpg', __FILE__) ?>" fileSize="16831" type="image/jpeg" medium="image" width="50" height="40"/>
	</item>
	<item>
		<title>2</title>
		<description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Amazement&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/2.jpg', __FILE__) ?>" title="Amazement"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?>" width="446" height="640" alt="Amazement" title="Amazement" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
		<link><?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?></link>
		<guid>2</guid>
		<media:group>
			<media:content url="<?php echo plugins_url('Slideshow/images/2.jpg', __FILE__) ?>" fileSize="91128" type="image/jpeg" medium="image" width="446" height="640"/>
			<media:thumbnail url="<?php echo plugins_url('Slideshow/images/2t.jpg', __FILE__) ?>" fileSize="16769" type="image/jpeg" medium="image" width="50" height="40"/>
		</media:group>
	</item>
	<item>
		<title>3</title>
		<description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Landscape&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/3.jpg', __FILE__) ?>" title="Landscape"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?>" width="777" height="582" alt="Landscape" title="Landscape" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
		<link><?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?></link>
		<guid>3</guid>
		<media:content url="<?php echo plugins_url('Slideshow/images/3.jpg', __FILE__) ?>" fileSize="118575" type="image/jpeg" medium="image" width="777" height="582">
			<media:thumbnail url="<?php echo plugins_url('Slideshow/images/3t.jpg', __FILE__) ?>" fileSize="16807" type="image/jpeg" medium="image" width="50" height="40"/>
		</media:content>
	</item>
	<item>
		<title>4</title>
		<description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Tasty Spoon&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/4.jpg', __FILE__) ?>" title="Tasty Spoon"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?>" width="800" height="600" alt="Tasty Spoon" title="Tasty Spoon" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
		<link><?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?></link>
		<guid>4</guid>
		<media:group>
			<media:content url="<?php echo plugins_url('Slideshow/images/4.jpg', __FILE__) ?>" fileSize="120587" type="image/jpeg" medium="image" width="800" height="600">
				<media:thumbnail url="<?php echo plugins_url('Slideshow/images/4t.jpg', __FILE__) ?>" fileSize="17448" type="image/jpeg" medium="image" width="50" height="40"/>
			</media:content>
		</media:group>
	</item>
</channel>
</rss>
<?php } ?>