<?php

class MovePlanText extends Doctrine_Migration_Base
{
    public function preUp()
    {
        $q = Doctrine_Query::create()
            ->select('a.userid, a.plan, a.edit_text, a.username')
            ->from('Accounts a');
        $plans = $q->execute();

        foreach ($plans as $plan) {
            $p = new Plans();
            $p->user_id = $plan->userid;
            try {
                $p->edit_text = stripslashes($plan->edit_text);
                $p->save();
            } catch (Doctrine_Validator_Exception $e) {
                echo "[$plan->username]'s Plan did not update because the generated HTML was too long\n";
                $p->plan = stripslashes($plan->plan);
                $p->save();
            }
        }
    }

    public function up()
    {
        $this->removeColumn('accounts', 'plan');
        $this->removeColumn('accounts', 'edit_text');

		$this->addIndex('plans', 'plans_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             'type' => 'unique',
             ));
    }

    public function down()
    {
		$this->removeIndex('plans', 'plans_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));

        $this->addColumn('accounts', 'plan', 'string', array('length' => '2147483647'));
        $this->addColumn('accounts', 'edit_text', 'string', array('length' => '2147483647'));
    }

    public function postDown()
    {
        $q = Doctrine_Query::create()
            ->select('p.user_id, p.plan, p.edit_text')
            ->from('Plans p');
        $plans = $q->execute();

        foreach ($plans as $plan) {
            $q = Doctrine_Query::create()
                ->update('Accounts')
                ->set('plan', '?', addslashes($plan->plan))
                ->set('edit_text', '?', addslashes($plan->edit_text))
                ->where('userid = ?', $plan->user_id);
            $q->execute();
        }
    }
}

