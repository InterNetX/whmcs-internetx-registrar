<?php

namespace App;

use Domainrobot\Domainrobot;
use Domainrobot\Service\DomainrobotService;
use Domainrobot\Model\Query;
use Domainrobot\Lib\DomainrobotAuth;
use Domainrobot\Lib\DomainrobotException;
use App\Exceptions\RegistrarModuleException;

require_once('bootstrap.php');

try {
    $domainrobot = new Domainrobot([
        "url" => "https://api.demo.autodns.com/v1",
        "auth" => new DomainrobotAuth([
            "user"     => '0000_Domainpoint_cb',
            "password" => 'domains',
            "context"  => 1,
        ])
    ]);

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


  // bjb.video
  // DONUTS-BBB+
  $customService =  new DomainrobotService($domainrobot->getDomainrobotConfig());
  $promise = $customService->sendRequest(
    $customService->domainrobotConfig->getUrl() ."/domainpremium/bjb.video",
    'GET'
  );
  $result = $promise->wait();
  print_r($result);

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
