<?php
/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('savemypaquet_zones')} ;
CREATE TABLE {$this->getTable('savemypaquet_zones')}(
`id_zone` int(11) unsigned NOT NULL auto_increment,
`nom` varchar(255) NOT NULL default'',
`deps` varchar(255) NOT NULL default'',
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY(`id_zone`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('savemypaquet_frais_port')} ;
CREATE TABLE {$this->getTable('savemypaquet_frais_port')} (
`id` int(11) unsigned NOT NULL auto_increment,
`id_zone`  int(11) NOT NULL default '0' ,
`id_service`  int(11) NOT NULL default '0' ,
`condition` varchar(255) NOT NULL default '',
`min` varchar(255) NOT NULL default'',
`max` varchar(255) NOT NULL  default'',
`cout` varchar(255) NOT NULL default'',
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY(`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('savemypaquet_services')} ;
CREATE TABLE {$this->getTable('savemypaquet_services')}(
`id_service` int(11) unsigned NOT NULL auto_increment,
`code_service` varchar(255) NOT NULL default '',
`active` int(11) NOT NULL default '1',
`nom` varchar(255) NOT NULL default '',
`titre` varchar(255) NOT NULL default '',
`price_type` int(11) NOT NULL default '3',
`price` varchar(255)  NULL,
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY(`id_service`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('savemypaquet_services')}` (`id_service`, `code_service` ,`active`, `nom`, `titre`,`price_type`,`price` ,`created_time`, `update_time`) VALUES
(1,'smp_optimum','1', 'Optimum', 'Optimum en 48 H', '3', NULL , NULL, NULL),
(2,'smp_premium','1', 'Premium', 'Premium avec suivi et photo comme preuve de livraison en 48 H','3',NULL, NULL, NULL),
(3, 'smp_premium_fast','1','Premium Fast', 'Premium Fast avec suivi et photo comme preuve de livraison en 24 H', '3',NULL,NULL, NULL);

INSERT INTO `{$this->getTable('savemypaquet_zones')}` (`id_zone`, `nom`, `deps`, `created_time`, `update_time`) VALUES
(1, 'Île de France', '75,77,78,93,94,95,91,92', NULL, NULL);



INSERT INTO `{$this->getTable('savemypaquet_frais_port')}` (`id`, `id_zone`, `id_service`, `condition`, `min`, `max`, `cout`, `created_time`, `update_time`) VALUES
(1, 1, 3, '0', '0', '0.25', '10.99', NULL, NULL),
(2, 1, 3, '0', '0.25', '0.5', '11.99', NULL, NULL),
(3, 1, 3, '0', '0.5', '0.75', '12.99', NULL, NULL),
(4, 1, 3, '0', '0.75', '1', '13.99', NULL, NULL),
(5, 1, 3, '0', '1', '2', '14.99', NULL, NULL),
(6, 1, 3, '0', '2', '5', '22.99', NULL, NULL),
(7, 1, 3, '0', '5', '10', '29.99', NULL, NULL),
(8, 1, 3, '0', '10', '30', '50.99', NULL, NULL);

INSERT INTO `{$this->getTable('savemypaquet_frais_port')}` (`id`, `id_zone`, `id_service`, `condition`, `min`, `max`, `cout`, `created_time`, `update_time`) VALUES
(9, 1, 2, '0', '0', '0.25', '6.99', NULL, NULL),
(10, 1, 2, '0', '0.25', '0.5', '7.99', NULL, NULL),
(11, 1, 2, '0', '0.5', '0.75', '8.99', NULL, NULL),
(12, 1, 2, '0', '0.75', '1', '9.99', NULL, NULL),
(13, 1, 2, '0', '1', '2', '10.99', NULL, NULL),
(14, 1, 2, '0', '2', '5', '15.99', NULL, NULL),
(15, 1, 2, '0', '5', '10', '20.99', NULL, NULL),
(16, 1, 2, '0', '10', '30', '30.99', NULL, NULL);

INSERT INTO `{$this->getTable('savemypaquet_frais_port')}` (`id`, `id_zone`, `id_service`, `condition`, `min`, `max`, `cout`, `created_time`, `update_time`) VALUES
(17, 1, 1, '0', '0', '0.25', '5.99', NULL, NULL),
(18, 1, 1, '0', '0.25', '0.5', '6.99', NULL, NULL),
(19, 1, 1, '0', '0.5', '0.75', '7.99', NULL, NULL),
(20, 1, 1, '0', '0.75', '1', '8.99', NULL, NULL),
(21, 1, 1, '0', '1', '2', '9.99', NULL, NULL),
(22, 1, 1, '0', '2', '5', '14.99', NULL, NULL),
(23, 1, 1, '0', '5', '10', '19.99', NULL, NULL),
(24, 1, 1, '0', '10', '30', '29.99', NULL, NULL);
");
$installer->addAttribute("order", "smyp_numero_colis", array("type" => "text"));
$installer->addAttribute("order", "smyp_statut_colis", array("type" => "text"));
$installer->addAttribute("order", "smyp_code_barre_colis", array("type" => "text"));
$installer->addAttribute("order", "smyp_selected_service", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_tel", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_batiment", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_etage", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_porte_position", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_porte_cote", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_digicode", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_digicode2", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_interphone", array("type" => "text"));
$installer->addAttribute("order", "smyp_client_email", array("type" => "text"));
$installer->endSetup();
