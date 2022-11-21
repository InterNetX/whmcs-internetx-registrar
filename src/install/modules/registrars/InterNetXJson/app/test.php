<?php

namespace App;

use Domainrobot\Domainrobot;
use Domainrobot\Model\Query;
use Domainrobot\Model\Domain;
use Domainrobot\Model\Redirect;
use Domainrobot\Model\QueryView;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Lib\DomainrobotAuth;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Service\DomainrobotService;
use App\Exceptions\RegistrarModuleException;
use Domainrobot\Model\CancelationTypeConstants;
use Domainrobot\Model\DomainCancelation;
use Domainrobot\Model\ExecutionTypeConstants;

require_once('bootstrap.php');

function getRedirects($domainrobot){
  $query = new Query([
      'filters' => [ new QueryFilter([
          'key' => 'domain',
          'value' => 'qwdqwdqwdqwd.com',
          'operator' => 'EQUAL'
      ])]
  ]);
   $redirectsOnServer = $domainrobot->redirect->list($query, ['title']);
   echo "REDIRECTS ON SERVER ----------------------------\n";
   print_r($redirectsOnServer);
}

function createAuthinfo1($domainrobot, $domain){
  $r = $domainrobot->domain->createAuthinfo1($domain);
  print_r($r);
}

try {
    $domainrobot = new Domainrobot([

        // "url" => "https://api.autodns.com/v1",
        "url" => "https://api.demo.autodns.com/v1",
        "auth" => new DomainrobotAuth([
            "user"     => '0000_Domainpoint_cb',
            "password" => 'domains',
            "context"  => 1,
        ])
    ]);

  // // Domain Cancellation
  // $dc = new DomainCancelation();
  // $dc->setDomain('qwdqwdqwdqwd.com');
  // $dc->setType(CancelationTypeConstants::DELETE);
  // $dc->setExecution(ExecutionTypeConstants::EXPIRE);
  // // $domainCancelation = $domainrobot->domainCancelation->create($dc);
  // // print_r($domainCancelation);

  // $query = new Query([
  //     'filters' => [ new QueryFilter([
  //         'key' => 'domain',
  //         'value' => 'qwdqwdqwdqwd.com',
  //         'operator' => 'EQUAL'
  //     ])]
  // ]);

  // $cancelationList = $domainrobot->domainCancelation->list($query);
  // print_r($cancelationList);

  // $domainrobot->domainCancelation->delete('qwdqwdqwdqwd.com');

  // $query = new Query([
  //     'filters' => [ new QueryFilter([
  //         'key' => 'domain',
  //         'value' => 'qwdqwdqwdqwd.com',
  //         'operator' => 'EQUAL'
  //     ])]
  // ]);

  // $cancelationList = $domainrobot->domainCancelation->list($query);
  // print_r($cancelationList);

  // History Query
  // $query =
  // new \Domainrobot\Model\Query([
  //       "view" => [
  //         "children" => 1,
  //         "offset" => 0,
  //         "limit" => 10,
  //         "from" => "2021-08-31T22:00:00.000Z",
  //         "to" => "2021-11-30T22:59:59.999Z"
  //       ],
  //       "orders" => [
  //         [
  //           "key" => "created",
  //           "type" => "DESC"
  //         ]
  //       ]
  //         ]);
  // print_r(json_encode($query->toArray(), JSON_PRETTY_PRINT));
  // $r = $domainrobot->job->historyList($query);
  try {

    createAuthinfo1($domainrobot, 'somedomainas.de');
    $domain = $domainrobot->domain->info('somedomainas.de');
    print_r($domain);

    // getRedirects($domainrobot);


    // // Domainrobot\Model\Redirect
    // $redirect = new Redirect();
    // // Set the source domain
    // $redirect->setSource("newframe3.qwdqwdqwdqwd.com");
    // // Set the target path
    // $redirect->setTarget("example-2.com/test");
    // // Set type DOMAIN
    // $redirect->setType("DOMAIN");
    // // Set mode HTTP
    // $redirect->setMode("FRAME");
    // $redirect->setTitle("Moin Moin");
    // // Domainrobot\Model\Redirect
    // $response = $domainrobot->redirect->create($redirect);
    // var_dump($response);

    // getRedirects($domainrobot);
    // $domainrobot->redirect->delete("newframe3.qwdqwdqwdqwd.com");







    // $response = $domainrobot->redirect->update($oneRedirect);
    // var_dump($response);


    // $redirect = $domainrobot->redirect->info("qwdqwdqwdqwd.com");
    // $redirect->setTarget("othertarget.com");
    // $response = $domainrobot->redirect->update($redirect);
    // => Call to undefined method Domainrobot\Model\Redirect::getId(



    // print_r($object);

    // $domainPremium = $domainrobot->domainPremium->info('bjb.video');
    // if (is_object($domainPremium) && $priceClass = $domainPremium->getPriceClass()){
    //   $premiumDomainPriceClass = $priceClass;
    //   $isPremiumDomain = true;
    //   echo 'is Premium';

    // }

  } catch (DomainrobotException $th) {
    //TODO: Move output to Module log
    print_r($th);

  }

} catch (RegistrarModuleException $th) {
    return print_r($th->getMessage());
  }
  catch (DomainrobotException $th) {
    //TODO: Move output to Module log
    return print_r("[".$th->getStatusCode()."] ". json_encode($th->getError(), JSON_PRETTY_PRINT));
  }
  catch (\Throwable $th) {
    return print_r($th->getMessage());
  }
