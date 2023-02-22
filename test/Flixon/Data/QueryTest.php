<?php

namespace Flixon\Data;

use Flixon\Common\Collections\Enumerable;
use Flixon\Data\Annotations\ClassMap;
use Flixon\Testing\TestCase;

class QueryTest extends TestCase {
    use \Flixon\Testing\Traits\Database {
        setUp as setUpBase;
    }

    public function setUp(): void {
        // Call the parent.
        $this->setUpBase();

        // Setup some test data.
        $this->db->execute("INSERT INTO `roles` (`name`) VALUES ('Admin')");
        $this->db->execute("INSERT INTO `users` (`roleId`, `username`, `salt`, `password`, `fullName`, `email`) VALUES (1, 'test', 'OOedJElGBV', '9d72dabe159a147e8113cbc143afd11519427746e1c7942c5f1c4386187bb0fc', 'Test', 'test@test.com');");
    }

    public function testAll() {
        // Act: Create the blog post.
        $blogPost = (new BlogPostExtended(1))->set(['name' => 'Test', 'content' => 'Content', 'active' => 1, 'authorId' => 1, 'dateAdded' => time()]);
        
        // Assert: Make sure the blog post has been set correctly.
        $this->assertEquals('Test Extended - Content', $blogPost->name . ' - ' . $blogPost->content);
        
        // Assert: Make sure the id has not been set.
        $this->assertFalse(isset($blogPost->id));

        // Act: Insert the blog post.
        $result = Query::insert($blogPost);
        
        // Assert: Make sure the insert was successful.
        $this->assertNotFalse($result);

        // Assert: Make sure the id has been set.
        $this->assertTrue(isset($blogPost->id));

        // Assert: Make sure the id is greater than 0.
        $this->assertGreaterThan(0, $blogPost->id);
        
        // Assert: Make sure there are no dirty properties.
        $this->assertEmpty($blogPost->dirtyProperties);

        // Act: Update the blog post.
        $blogPost->name = 'Test Update 1';
        $blogPost->content = 'Content Update 1';
        
        // Assert: Make sure there are 2 dirty properties.
        $this->assertCount(2, $blogPost->dirtyProperties);
       
        // Act: Update the blog post.
        $result = Query::update($blogPost);
        
        // Assert: Make sure the update was successful.
        $this->assertNotFalse($result);

        // Act: Try to get the blog post.
        $blogPost = Query::from(BlogPostExtended::class, $blogPost->id)->fetch();

        // Assert: Make sure the correct name and content is returned.
        $this->assertEquals('Test Update 1 Extended - Content Update 1', $blogPost->name . ' - ' . $blogPost->content);

        // Act: Update the blog post.
        $blogPost->set(['name' => 'Test Update 2', 'content' => 'Content Update 2']);
        $result = Query::update($blogPost);

        // Assert: Make sure the update was successful.
        $this->assertNotFalse($result);

        // Act: Try to get the blog post.
        $blogPost = Query::from(BlogPostExtended::class, $blogPost->id)->fetch();

        // Assert: Make sure the correct name and content is returned.
        $this->assertEquals('Test Update 2 Extended - Content Update 2', $blogPost->name . ' - ' . $blogPost->content);
    }

    public function testFetch() {
        // Arrange: Add some test data.
        $this->db->execute("INSERT INTO `users` (`roleId`, `username`, `salt`, `password`, `fullName`, `email`) VALUES (1, 'test2', 'OOedJElGBV', '9d72dabe159a147e8113cbc143afd11519427746e1c7942c5f1c4386187bb0fc', 'Test', 'test2@test.com');");

        // Act
        $result = Query::from(User::class, 2)->fetch()->username;

        // Assert: Make sure only one query is executed.
        $this->assertEquals('test2', $result);
    }

    public function testFetchAndUpdateMultiplePrimaryKeys() {
        // Arrange: Add some test data.
        $this->db->execute("INSERT INTO `jobs` (`name`, `commission`, `active`) VALUES ('Job 1', 0, 1);");
        $this->db->execute("INSERT INTO `jobs` (`name`, `commission`, `active`) VALUES ('Job 2', 0, 1);");
        $this->db->execute("INSERT INTO `user_jobs` (`userId`, `jobId`, `receiveEmail`) VALUES (1, 1, 1)");
        $this->db->execute("INSERT INTO `user_jobs` (`userId`, `jobId`, `receiveEmail`) VALUES (1, 2, 1)");

        // Act: Get the user job.
        $userJob = Query::from(UserJob::class, [1, 2])->fetch();

        // Assert: Make sure the correct job is returned.
        $this->assertEquals('Job 2', $userJob->job->name);

        // Act: Update the user job.
        $userJob->receiveEmail = 0;
        $result = Query::update($userJob);

        // Assert: Make sure the update was successful.
        $this->assertNotFalse($result);

        // Act: Get the user figter.
        $userJob = Query::from(UserJob::class, [1, 2])->fetch();

        // Assert: Make sure it has been updated.
        $this->assertEquals(0, $userJob->receiveEmail);
    }

    public function testFetchColumn() {
        // Arrange: Add some test data.
        $this->db->execute("INSERT INTO `users` (`roleId`, `username`, `salt`, `password`, `fullName`, `email`) VALUES (1, 'test2', 'OOedJElGBV', '9d72dabe159a147e8113cbc143afd11519427746e1c7942c5f1c4386187bb0fc', 'Test', 'test2@test.com');");
        $this->db->execute("INSERT INTO `users` (`roleId`, `username`, `salt`, `password`, `fullName`, `email`) VALUES (1, 'test3', 'OOedJElGBV', '9d72dabe159a147e8113cbc143afd11519427746e1c7942c5f1c4386187bb0fc', 'Test', 'test2@test.com');");

        // Act
        $result = Query::from(User::class)->select(null)->select('SUM(roleId)')->fetchColumn();

        // Assert: Make sure the correct result is returned.
        $this->assertEquals(3, $result);
    }

    public function testHasOne() {
        // Arrange: Add some test data.
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 1', 'Content', 1, 1, 1411487603);");
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 2', 'Content', 1, 1, 1411487603);");
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 3', 'Content', 1, 1, 1411487603);");

        // Act: Create the query.
        $query = Query::from(BlogPost::class)
            ->orderBy('dateAdded DESC')
            ->limit(2);

        foreach ($query as $blogPost) {
            // Assert: Make sure the author is correct.
            $this->assertEquals('test', $blogPost->author->username);

            // Assert: Make sure the role is correct.
            $this->assertEquals('Admin', $blogPost->author->role->name);

            // Assert: Make sure the category is correct.
            $this->assertNull($blogPost->category);
        }

        // Assert: Make sure n * 2 (user and role, not category as the categoryId is null) + 1 queries are executed.
        $this->assertCount(5, $this->queries);
    }

    public function testHasMany() {
        // Act
        $result = Query::from(User::class, 1)->fetch()->addresses->count();

        // Assert: Make sure no data returned.
        $this->assertEquals(0, $result);

        // Assert: Make sure two queries are executed.
        $this->assertCount(2, $this->queries);
    }

    public function testSetHasOne() {
        // Arrange: Create the user address.
        $userAddress = (new UserAddress())->set([
            'userId' => 1,
            'default' => 1,
            'address' => (new Address())->set([
                'fullName' => 'Test',
                'addressLine1' => 'Test',
                'addressLine2' => 'Test',
                'town' => 'Test',
                'county' => 'Test',
                'postcode' => 'Test'
            ])
        ]);

        // Insert the address.
        $result = Query::insert($userAddress->address);

        // Assert: Make sure the insert was successful.
        $this->assertNotFalse($result);

        // Set the address id against the user address.
        $userAddress->addressId = $userAddress->address->id;

        // Insert the user address.
        $result = Query::insert($userAddress);

        // Assert: Make sure the insert was successful.
        $this->assertNotFalse($result);
    }

    public function testWith() {
        // Arrange: Add some test data.
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 1', 'Content', 1, 1, 1411487603);");
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 2', 'Content', 1, 1, 1411487603);");
        $this->db->execute("INSERT INTO `blog_posts` (`name`, `content`, `active`, `authorId`, `dateAdded`) VALUES ('Test 3', 'Content', 1, 1, 1411487603);");

        // Act: Create the query.
        $query = Query::from(BlogPost::class)
            ->with('author', 'users')
            ->with('author.role')
            ->with('category')
            ->orderBy('dateAdded DESC')
            ->limit(2);

        foreach ($query as $blogPost) {
            // Assert: Make sure the author is correct.
            $this->assertEquals('test', $blogPost->author->username);

            // Assert: Make sure the role is correct.
            $this->assertEquals('Admin', $blogPost->author->role->name);

            // Assert: Make sure the category is correct.
            $this->assertNull($blogPost->category);
        }

        // Assert: Make sure only one query is executed.
        $this->assertCount(1, $this->queries);
    }
}

class Address extends Entity { }

class BlogPost extends Entity {
    public function __construct(int $authorId = null) {
        // Set the default properties.
        $this->authorId        = $authorId;
        $this->dateAdded     = time();
    }

    public function getCategory() {
        return $this->hasOne(Category::class);
    }

    public function getAuthor(): User {
        return $this->hasOne(User::class);
    }
}

#[ClassMap('blog_posts')]
class BlogPostExtended extends BlogPost {
    public function getName():string {
        return $this->properties['name'] . ' Extended';
    }

    public function setName($value) {
        $this->properties['name'] = $value;
    }
}

class Category extends Entity {
    public function getParent() {
        return $this->hasOne(Category::class);
    }

    public function getChildren(): Enumerable {
        return $this->hasMany(Category::class, 'parentId');
    }
}

class Job extends Entity {
    public function getUsers(): Enumerable {
        return $this->lazy(function() {
            return Query::from(UserJob::class)->with('user')->where('jobId', $this->id)->orderBy('user.fullName')->fetchAll();
        });
    }
}

class Role extends Entity { }

class User extends Entity {
    public function getFirstName(): string {
        return Utilities::splitName($this->fullName)[0];
    }

    public function getLastName(): string {
        return Utilities::splitName($this->fullName)[1];
    }

    public function getRole(): Role {
        return $this->hasOne(Role::class);
    }

    public function getAddresses(): Enumerable {
        return $this->hasMany(UserAddress::class);
    }

    public function getJobs(): Enumerable {
        return $this->lazy(function() {
            return Query::from(UserJob::class)->with('job')->where('userId', $this->id)->orderBy('job.name')->fetchAll();
        });
    }
}

class UserAddress extends Entity {
    public function __construct() {
        // Set the default properties.
        $this->dateAdded = time();
    }

    public function getAddress(): Address {
        return $this->hasOne(Address::class);
    }

    public function setAddress(Address $value) {
        $this->address = $value;
    }
}

class UserJob extends Entity {
    public function getUser(): User {
        return $this->hasOne(User::class);
    }

    public function getJob(): Job {
        return $this->hasOne(Job::class);
    }
}