<?php
declare(strict_types=1);

namespace Tests\Unit\Services\Collection;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Services\Collection\ItemInEntityCollectionService;
use Tests\TestCase;

final class ItemInEntityCollectionServiceTest extends TestCase
{
    public function testIsItemInEntityCollectionReturnsFalseWithoutCollections(): void
    {
        $service = new ItemInEntityCollectionService();

        $user = new User([
            'collections' => collect([]),
        ]);

        $item = new Subscription();

        $result = $service->isItemInEntityCollection($user, $item);

        $this->assertFalse($result);
    }

    public function testIsItemInEntityCollectionReturnsFalseWithoutItemInCollections(): void
    {
        $service = new ItemInEntityCollectionService();

        $user = new User([
            'collections' => collect([
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem()
                    ])
                ]),
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem()
                    ])
                ]),
            ]),
        ]);

        $item = new Subscription([
            'id' => 23423,
        ]);

        $result = $service->isItemInEntityCollection($user, $item);

        $this->assertFalse($result);
    }

    public function testIsItemInEntityCollectionReturnsFalseWithItemTypeMismatch(): void
    {
        $service = new ItemInEntityCollectionService();

        $user = new User([
            'collections' => collect([
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'verification_code',
                            'item_id' => 43,
                        ]),
                    ])
                ]),
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'verification_code',
                            'item_id' => 23423,
                        ]),
                    ])
                ]),
            ]),
        ]);

        $item = new Subscription([
            'id' => 23423,
        ]);

        $result = $service->isItemInEntityCollection($user, $item);

        $this->assertFalse($result);
    }

    public function testIsItemInEntityCollectionReturnsFalseWithItemIdMismatch(): void
    {
        $service = new ItemInEntityCollectionService();

        $user = new User([
            'collections' => collect([
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'verification_code',
                            'item_id' => 43,
                        ]),
                    ])
                ]),
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'subscription',
                            'item_id' => 435,
                        ]),
                    ])
                ]),
            ]),
        ]);

        $item = new Subscription([
            'id' => 23423,
        ]);

        $result = $service->isItemInEntityCollection($user, $item);

        $this->assertFalse($result);
    }

    public function testIsItemInEntityCollectionReturnsTrue(): void
    {
        $service = new ItemInEntityCollectionService();

        $user = new User([
            'collections' => collect([
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'verification_code',
                            'item_id' => 43,
                        ]),
                    ])
                ]),
                new Collection([
                    'collectionItems' => collect([
                        new CollectionItem([
                            'item_type' => 'subscription',
                            'item_id' => 23423,
                        ]),
                    ])
                ]),
            ]),
        ]);

        $item = new Subscription([
            'id' => 23423,
        ]);

        $result = $service->isItemInEntityCollection($user, $item);

        $this->assertTrue($result);
    }
}