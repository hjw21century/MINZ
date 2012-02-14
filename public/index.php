<?php
# ***** BEGIN LICENSE BLOCK *****
# MINZ - A free PHP framework
# Copyright (C) 2011 Marien Fressinaud
# 
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# ***** END LICENSE BLOCK *****

// Constantes de chemins
define('PUBLIC_PATH', realpath(dirname(__FILE__)));
define('LIB_PATH', realpath(PUBLIC_PATH.'/../lib'));
define('APP_PATH', realpath(PUBLIC_PATH.'/../app'));
define('LOG_PATH', realpath(PUBLIC_PATH.'/../log'));

// Ajout du répertoire /lib dans l'include_path
set_include_path(get_include_path() . PATH_SEPARATOR . LIB_PATH);

// Paramètres du lancement de l'application
$params = array(
    'config_file'   => APP_PATH.'/configuration/application.ini'
);

require_once('Bootstrap.class.php');

$bootstrap = Bootstrap::getInstance($params);
$bootstrap->init(); // lancement de l'application

