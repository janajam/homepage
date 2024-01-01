<?php

namespace App\Enums ;

enum Status : string {
    case ReceivedIt = 'ReceivedIt' ;
    case InPreparation  = 'InPreparation' ;
    case OrderSent = 'OrderSent' ;

}
