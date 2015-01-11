<?php

class Block extends BaseBlock
{
    public static function addBlock($blocker, $to_block) {
        $block = new Block();
        $block->blocking_user_id = $blocker;
        $block->blocked_user_id = $to_block;
        $block->save();
    }

    public static function removeBlock($blocker, $to_unblock) {
        Doctrine_Query::create()
            ->delete("Block b")
            ->where("b.blocking_user_id = ?", $blocker)
            ->andWhere("b.blocked_user_id = ?", $to_unblock)
            ->execute();
    }

    public static function isBlocking($blocker, $blocked) {
        $q = Doctrine_Query::create()
            ->select("count(*) as c")
            ->from("Block")
            ->where("blocking_user_id = ?", $blocker)
            ->andWhere("blocked_user_id = ?", $blocked);
        return ($q->fetchOne()->c > 0);
    }

    public static function allUserIdsWithBlockingRelationships($user_id) {
        $q = Doctrine_Query::create()
            ->select("*")
            ->from("Block")
            ->where("blocking_user_id = ?", $user_id)
            ->orWhere("blocked_user_id = ?", $user_id);
        $ids = [];
        foreach ($q->fetchArray() as $row) {
            if ($row["blocking_user_id"] == $user_id) {
                array_push($ids, $row["blocked_user_id"]);
            } else {
                array_push($ids, $row["blocking_user_id"]);
            }
        }
        return array_unique($ids);
    }
}
