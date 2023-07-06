<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            self::assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect();
        $collection->push(1, 2, 3);
        assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        assertEquals(3, $result);
        assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEquals([2, 4, 6], $result->all());
    }

    public function testMapinto()
    {
        $collection = collect(["Adrian"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person('Adrian')], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ["Adriansyah", "Suryawan"],
            ["Haikal", "Dwiki"]
        ]);

        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . " " . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Adriansyah Suryawan"),
            new Person("Haikal Dwiki"),
        ], $result->all());
    }

    // public function testMapToGroup()
    // {
    //     $collection = collect([
    //         [
    //             "name" => "Adrian",
    //             "department" => "IT"
    //         ],
    //         [
    //             "name" => "Chandra",
    //             "department" => "IT"
    //         ],
    //         [
    //             "name" => "Haikal",
    //             "department" => "HR"
    //         ],
    //     ]);

    //     $result = $collection->mapToGroup(function ($person) {
    //         return [$person['department'], $person['name']];
    //     });
    //     assertEquals([
    //         "IT" => collect(["Adriansyah", "Suryawan"]),
    //         "HR" => collect(["Haikal"])
    //     ], $result->all());
    // }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6]),
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEquals([
            1, 2, 3, 4, 5, 6
        ], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "country"]);
        $collection2 = collect(["Adrian", "Indonesia"]);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name" => "Adrian",
            "country" => "Indonesia",
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection  = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $result = $collection->collapse();
        assertEquals([
            1, 2, 3, 4, 5, 6, 7, 8, 9
        ], $result->all());
    }

    public function testFlatmap()
    {
        $collection = collect([
            [
                "name" => "Adrian",
                "hobbies" => [
                    "Gaming",
                    "Coding"
                ]
            ],
            [
                "name" => "Haikal",
                "hobbies" => [
                    "Guitaring",
                    "Singing"
                ]
            ]
        ]);
        $hobbies = $collection->flatmap(function ($item) {
            return $item['hobbies'];
        });
        assertEquals(["Gaming", "Coding", "Guitaring", "Singing"], $hobbies->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["Muhamad", "Adriansyah", "Suryawan"]);

        assertEquals("Muhamad-Adriansyah-Suryawan", $collection->join('-'));
        assertEquals("Muhamad-Adriansyah_Suryawan", $collection->join('-', '_'));
    }

    public function testFilter()
    {
        $collection = collect([
            "Adrian" => "100",
            "Haikal" => "80",
            "Chandra" => "90"
        ]);
        $result = $collection->filter(function ($item, $key) {
            return $item >= 90;
        });
        assertEquals([
            "Adrian" => "100",
            "Chandra" => "90"
        ], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "Adrian" => "100",
            "Haikal" => "80",
            "Chandra" => "90"
        ]);
        [$result1, $result2] = $collection->partition(function ($item, $key) {
            return $item >= 90;
        });
        assertEquals([
            "Adrian" => "100",
            "Chandra" => "90"
        ], $result1->all());
        assertEquals([
            "Haikal" => "80",
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(["Adrian", "Haikal", "Chandra"]);
        self::assertTrue($collection->contains("Adrian"));
        self::assertTrue($collection->contains(function ($value, $key) {
            return $value == "Haikal";
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                "name" => "Adrian",
                "department" => "IT"
            ],
            [
                "name" => "Chandra",
                "department" => "IT"
            ],
            [
                "name" => "Haikal",
                "department" => "HR"
            ]
        ]);

        $result = $collection->groupBy("department");

        assertEquals([
            "IT" => collect([
                [
                    "name" => "Adrian",
                    "department" => "IT"
                ],
                [
                    "name" => "Chandra",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect(
                [
                    [
                        "name" => "Haikal",
                        "department" => "HR",
                    ],
                ]
            )
        ], $result->all());

        $result = $collection->groupBy(function ($value, $key) {
            return $value["department"];
        });

        assertEquals([
            "IT" => collect([
                [
                    "name" => "Adrian",
                    "department" => "IT"
                ],
                [
                    "name" => "Chandra",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect(
                [
                    [
                        "name" => "Haikal",
                        "department" => "HR",
                    ],
                ]
            )
        ], $result->all());
    }

    public function testSice()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->slice(3);
        assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->slice(3, 2);
        assertEqualsCanonicalizing([4, 5], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->take(3);
        assertEqualsCanonicalizing([1, 2, 3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEqualsCanonicalizing([1, 2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEqualsCanonicalizing([1, 2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->skip(3);
        assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });
        assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });
        assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testChunked()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->chunk(3);

        assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
    }

    public function testFirst()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->first();
        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });
        $this->assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->last();
        $this->assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 6;
        });
        $this->assertEquals(5, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->random();

        $this->assertTrue(is_array([1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }

    public function testCheckingExistence()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertTrue($collection->isNotEmpty());
        self::assertFalse($collection->isEmpty());
        self::assertTrue($collection->contains(8));
        self::assertFalse($collection->contains(10));
        self::assertTrue(
            $collection->contains(function ($value, $key) {
                return $value == 8;
            })
        );
    }

    public function testOrdering()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->sort();
        assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->sortDesc();
        assertEqualsCanonicalizing([9, 8, 7, 6, 5, 4, 3, 2, 1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->sum();
        assertEquals(45, $result);

        $result = $collection->avg();
        assertEquals(5, $result);

        $result = $collection->min();
        assertEquals(1, $result);

        $result = $collection->max();
        assertEquals(9, $result);
    }

    public function testReduce()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        assertEquals(45, $result->all());
    }
}