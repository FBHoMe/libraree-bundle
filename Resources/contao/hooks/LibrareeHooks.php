<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 12.12.2017
 * Time: 08:45
 */

namespace Home\LibrareeBundle\Resources\contao\hooks;
use Home\LibrareeBundle\Resources\contao\models\BasePinModel;

class LibrareeHooks extends \Frontend
{

    public function sendCartEmails($arrSubmitted, $arrFiles, $intOldId, $arrForm, $arrLabels)
    {
        if($arrSubmitted['Bestellung']){
            #-- mailer info
            $recipientSystem = $arrSubmitted['recipientSystem'];
            $subjectCustomer = $arrSubmitted['subjectCustomer'];
            $subjectSystem = $arrSubmitted['subjectSystem'];
            $from = $arrSubmitted['from'];
            $fromName = $arrSubmitted['fromName'];

            #-- get products and user
            $total = 0;
            $order = json_decode(str_replace('&#125;','}',$arrSubmitted['Bestellung']));
            $bemerkung = $arrSubmitted['Bemerkung'];

            $products = array();
            // todo replace with actual model
            $pins = BasePinModel::findAll();
            if($pins){
                $pins = $pins->fetchAll();
                if(is_array($pins) && count($pins) > 0){
                    foreach ($pins as $pin){
                        $products[$pin['id']] = $pin;
                    }
                }
            }

            $readable = '<table><tr>
				    <td>Artikel</td><td>Menge</td><td>Einzelpreis</td><td>Gesamtpreis</td>
			    </tr>';

            foreach($order as $item){
                $id = $item->id;
                $price = $products[$id]['price'];
                $sum = $price * $item->count;
                $total = $total + $sum;

                $readable .= '<tr><td>'.$products[$id]['title'].'</td><td>'.$item->count.'</td><td>'.number_format($price, 2). '€</td><td>'.number_format($sum, 2).'€</td></tr>';
            }
            $readable .= '<tr><td> </td><td> </td><td> </td><td><strong>Gesammtsumme<br>'.number_format($total, 2).'€</strong></td></tr>';
            $readable .= '</table><div>Bemerkung: ' . $bemerkung . '</div>';

            #-- Anschrift erstellen
            $objUser = \FrontendUser::getInstance();
            //var_dump($objUser);
            $anschrift = '<div class="anschrift">';

            if($objUser->gender === 'male'){
                $gender = 'Herr ';
            }elseif($objUser->gender === 'female'){
                $gender = 'Frau ';
            }

            $anschrift .= $gender;
            $anschrift .= $objUser->firstname.' '.$objUser->lastname.'<br>';
            $anschrift .= $objUser->company != '' ? $objUser->company.'<br>' : '';
            $anschrift .= $objUser->street != '' ? $objUser->street.'<br>' : '';
            $anschrift .= $objUser->postal != '' ? $objUser->postal.' ' : '';
            $anschrift .= $objUser->city != '' ? $objUser->city.'<br>' : '<br>';
            $anschrift .= $objUser->phone != '' ? $objUser->phone.'<br>' : '<br>';
            $anschrift .= $objUser->mobile != '' ? $objUser->mobile.'<br>' : '<br>';
            $anschrift .= $objUser->email != '' ? $objUser->email.'<br>' : '<br>';
            $anschrift .= '</div>';

            #-- Bestellung in der Datenbank speichern
            $sql = '
                    INSERT INTO tl_order ( tstamp, orderJson, userId, firstname, lastname, company, street, postal, city,
                    email, phone, total)
                    VALUES (
                      \''.time().'\',
                      \''.json_encode(str_replace('&#125;','}',$arrSubmitted['Bestellung'])) . '\',
                      \''.$objUser->id.'\',
                      \''.$objUser->firstname.'\',
                      \''.$objUser->lastname.'\',
                      \''.$objUser->company.'\',
                      \''.$objUser->street.'\',
                      \''.$objUser->postal.'\',
                      \''.$objUser->city.'\',
                      \''.$objUser->email.'\',
                      \''.$objUser->phone.'\',
                      \''.number_format($total, 2).'\'
                    )
                ';

            $orderId = \Database::getInstance()
                ->prepare($sql)
                ->execute()
                ->insertId
            ;

            #-- Header
            $header = '<div class="header"><h1>Bestellung</h1><p>Bestellnummer: '.$orderId.'</p>' .
                '<p>Kundennummer: '.$objUser->id.'</p></div>';

            $arrSubmitted['email'] = $objUser->email;
            $arrSubmitted['Bestellnummer'] = $orderId;
            $arrSubmitted['Anschrift'] = $header . $anschrift;
            $arrSubmitted['Bestellung'] = $readable;

            #-- send mails
            $mailer = new \Contao\Email();
            $mailer->subject = $subjectCustomer;
            $mailer->html = $anschrift . '<br>' . $readable;
            $mailer->from = $from;
            $mailer->fromName = $fromName;
            #-- send to user
            $mailer->sendTo($objUser->email);
            #-- send to system
            $mailer->subject = $subjectSystem;
            $mailer->sendTo($recipientSystem);

        }
    }

}