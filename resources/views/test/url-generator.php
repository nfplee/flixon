<ul>
    <li><?php echo $this->url->generate('home'); ?> | <?php echo $this->url->generate('home', ['_locale' => 'en']); ?> | <?php echo $this->url->generate('home', ['_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('home', ['slug' => 'foo']); ?> | <?php echo $this->url->generate('home', ['slug' => 'foo', '_locale' => 'en']); ?> | <?php echo $this->url->generate('home', ['slug' => 'foo', '_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('details', ['slug' => 'foo']); ?> | <?php echo $this->url->generate('details', ['slug' => 'foo', '_locale' => 'en']); ?> | <?php echo $this->url->generate('details', ['slug' => 'foo', '_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('blog_home'); ?> | <?php echo $this->url->generate('blog_home', ['_locale' => 'en']); ?> | <?php echo $this->url->generate('blog_home', ['_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('blog_home', ['slug' => 'foo']); ?> | <?php echo $this->url->generate('blog_home', ['slug' => 'foo', '_locale' => 'en']); ?> | <?php echo $this->url->generate('blog_home', ['slug' => 'foo', '_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('jobs_home'); ?> | <?php echo $this->url->generate('jobs_home', ['_locale' => 'en']); ?> | <?php echo $this->url->generate('jobs_home', ['_locale' => 'de']); ?></li>
    <li><?php echo $this->url->generate('jobs_home', ['slug' => 'foo']); ?> | <?php echo $this->url->generate('jobs_home', ['slug' => 'foo', '_locale' => 'en']); ?> | <?php echo $this->url->generate('jobs_home', ['slug' => 'foo', '_locale' => 'de']); ?></li>
</ul>