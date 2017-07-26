<?php
use Phalcon\Cli\Task;
use Phalcon\Ext\Mailer\Manager;
use Phalcon\Di\Injectable;
class MainTask extends Task
{
   
 public function sendEmailAction() {
       // The email setting
		$config = [
			'driver' => 'smtp',
			'host' => 'smtp.live.com',
			'port' => 587,
			'encryption' => 'tls',
			'username' => 'algo@hotmail.com',
			'password' => 'fewfwefrw',
			'from' => [
				'email' => 'algo@hotmail.com',
				'name' => 'XXXXXX',
			],
		];
		$mailer = new Manager($config);
        
        // Inspect the next ready job.
		$message = $mailer->createMessage()
			->to('otroalgo@gmail.com', 'XXXXXX')
			->subject('Reminder!')
			->content('As you can see in the table of the link above, background-image, does not work in the most used mail clients currently: hotmail');
		// Send message
		$message->send();
	
}
// Check the queue from Beanstalk and send the email scheduled there
public function mailQueueAction()
    {
    	   //  The setting queue
		$queue = new Phalcon\Queue\Beanstalk(
           [
                'host' => '127.0.0.1',
                'port' => '11300'
           ]
        );
              $queue->put(
        	  [
              'sendEmail' => rand(),
              ],

              [
                 'priority' => 0,
                 'delay'    => 1,
                 'ttr'      => 10,
              ]
        );
        while (true) {
        	// job in the queue
            while ($job=$queue->peekReady() !== false) {
            	// reserve the job 
                $job =$queue->reserve();
                $message = $job->getBody();
                // $job =$this->sendEmailAction();
                $this->sendEmailAction();
                $job->delete();
            }
            sleep(5);
        }

    }
}
