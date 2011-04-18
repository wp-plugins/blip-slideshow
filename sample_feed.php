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

	if(function_exists('bloginfo')) { return false; }
	require_once(dirname(__FILE__).'/../../../wp-load.php');
	echo '<?xml version="1.0" encoding="utf-8"?>';?>

<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:exif="http://www.exif.org/specifications.html" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Holly's Slideshow</title>
    <link>http://www.jasonhendriks.com/programmer/HollysSlideshow/</link>
    <description></description>
    <generator>http://www.smugmug.com/</generator>
    <copyright>Copyright 2011, the copyright holder of each photograph.</copyright>
    <image>
      <url><?php echo plugins_url('/Slideshow/images/1t.jpg', __FILE__) ?></url>
      <title>Holly's Slideshow</title>
      <link>http://www.jasonhendriks.com/programmer/HollysSlideshow/</link>
    </image>
    <atom:link rel="self" type="application/rss+xml" href="<?php echo plugins_url('/sample_feed.php', __FILE__) ?>"/>
    <item>
      <title>Island Mountain</title>
      <link><?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?></link>
      <description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Island Mountain&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/1.jpg', __FILE__) ?>" title="Island Mountain"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?>" width="720" height="540" alt="Island Mountain" title="Island Mountain" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
      <author>nobody@smugmug.com (Holly's Slideshow)</author>
      <guid isPermaLink="false"><?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?></guid>
      <media:group>
        <media:content url="<?php echo plugins_url('Slideshow/images/1t.jpg', __FILE__) ?>" fileSize="17117" type="image/jpeg" medium="image" width="50" height="40">
          <media:hash algo="md5">c2b574ec41713369027b64c815346c64</media:hash>
        </media:content>
        <media:content url="<?php echo plugins_url('Slideshow/images/1.jpg', __FILE__) ?>" fileSize="85681" type="image/jpeg" medium="image" width="720" height="540" isDefault="true">
          <media:hash algo="md5">c1b9b093fa315cc9995c34f365c522d1</media:hash>
        </media:content>
      </media:group>
      <media:title type="html">Island Mountain</media:title>
      <media:text type="html">&lt;p&gt;&lt;a href="http://ambientphotography.smugmug.com"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Island Mountain&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/1.jpg', __FILE__) ?>" title="Island Mountain"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/1.jpg', __FILE__) ?>" width="720" height="540" alt="Island Mountain" title="Island Mountain" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</media:text>
      <media:thumbnail url="<?php echo plugins_url('Slideshow/images/1t.jpg', __FILE__) ?>" width="50" height="40"/>
    </item>
    <item>
      <title>Amazement</title>
      <link><?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?></link>
      <description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Amazement&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/2.jpg', __FILE__) ?>" title="Amazement"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?>" width="446" height="640" alt="Amazement" title="Amazement" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
      <author>nobody@smugmug.com (Holly's Slideshow)</author>
      <guid isPermaLink="false"><?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?></guid>
      <media:group>
        <media:content url="<?php echo plugins_url('Slideshow/images/2t.jpg', __FILE__) ?>" fileSize="17117" type="image/jpeg" medium="image" width="50" height="40">
          <media:hash algo="md5">c2b574ec41713369027b64c815346c64</media:hash>
        </media:content>
        <media:content url="<?php echo plugins_url('Slideshow/images/2.jpg', __FILE__) ?>" fileSize="85681" type="image/jpeg" medium="image" width="446" height="640" isDefault="true">
          <media:hash algo="md5">c1b9b093fa315cc9995c34f365c522d1</media:hash>
        </media:content>
      </media:group>
      <media:title type="html">Amazement</media:title>
      <media:text type="html">&lt;p&gt;&lt;a href="http://ambientphotography.smugmug.com"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Amazement&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/2.jpg', __FILE__) ?>" title="Amazement"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/2.jpg', __FILE__) ?>" width="446" height="640" alt="Amazement" title="Amazement" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</media:text>
      <media:thumbnail url="<?php echo plugins_url('Slideshow/images/2t.jpg', __FILE__) ?>" width="50" height="40"/>
    </item>
    <item>
      <title>Landscape</title>
      <link><?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?></link>
      <description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Landscape&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/3.jpg', __FILE__) ?>" title="Landscape"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?>" width="777" height="582" alt="Landscape" title="Landscape" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
      <author>nobody@smugmug.com (Holly's Slideshow)</author>
      <guid isPermaLink="false"><?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?></guid>
      <media:group>
        <media:content url="<?php echo plugins_url('Slideshow/images/3t.jpg', __FILE__) ?>" fileSize="17117" type="image/jpeg" medium="image" width="50" height="40">
          <media:hash algo="md5">c2b574ec41713369027b64c815346c64</media:hash>
        </media:content>
        <media:content url="<?php echo plugins_url('Slideshow/images/3.jpg', __FILE__) ?>" fileSize="85681" type="image/jpeg" medium="image" width="777" height="582" isDefault="true">
          <media:hash algo="md5">c1b9b093fa315cc9995c34f365c522d1</media:hash>
        </media:content>
      </media:group>
      <media:title type="html">Landscape</media:title>
      <media:text type="html">&lt;p&gt;&lt;a href="http://ambientphotography.smugmug.com"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Landscape&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/3.jpg', __FILE__) ?>" title="Landscape"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/3.jpg', __FILE__) ?>" width="777" height="582" alt="Landscape" title="Landscape" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</media:text>
      <media:thumbnail url="<?php echo plugins_url('Slideshow/images/3t.jpg', __FILE__) ?>" width="50" height="40"/>
    </item>
    <item>
      <title>Tasty Spoon</title>
      <link><?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?></link>
      <description>&lt;p&gt;&lt;a href="http://www.jasonhendriks.com/programmer/HollysSlideshow/"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Tasty Spoon&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/4.jpg', __FILE__) ?>" title="Tasty Spoon"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?>" width="800" height="600" alt="Tasty Spoon" title="Tasty Spoon" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</description>
      <author>nobody@smugmug.com (Holly's Slideshow)</author>
      <guid isPermaLink="false"><?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?></guid>
      <media:group>
        <media:content url="<?php echo plugins_url('Slideshow/images/4t.jpg', __FILE__) ?>" fileSize="17117" type="image/jpeg" medium="image" width="50" height="40">
          <media:hash algo="md5">c2b574ec41713369027b64c815346c64</media:hash>
        </media:content>
        <media:content url="<?php echo plugins_url('Slideshow/images/4.jpg', __FILE__) ?>" fileSize="85681" type="image/jpeg" medium="image" width="800" height="600" isDefault="true">
          <media:hash algo="md5">c1b9b093fa315cc9995c34f365c522d1</media:hash>
        </media:content>
      </media:group>
      <media:title type="html">Tasty Spoon</media:title>
      <media:text type="html">&lt;p&gt;&lt;a href="http://ambientphotography.smugmug.com"&gt;Holly's Slideshow&lt;/a&gt;&lt;br /&gt;Tasty Spoon&lt;/p&gt;&lt;p&gt;&lt;a href="<?php echo plugins_url('Slideshow/images/4.jpg', __FILE__) ?>" title="Tasty Spoon"&gt;&lt;img src="<?php echo plugins_url('/Slideshow/images/4.jpg', __FILE__) ?>" width="800" height="600" alt="Tasty Spoon" title="Tasty Spoon" style="border: 1px solid #000000;" /&gt;&lt;/a&gt;&lt;/p&gt;</media:text>
      <media:thumbnail url="<?php echo plugins_url('Slideshow/images/4t.jpg', __FILE__) ?>" width="50" height="40"/>
    </item>
 </channel>
</rss>
