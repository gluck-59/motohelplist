<?php
    
// доступные у нас языки (гы-гы)
$aLanguages = array(
    'en' => 'English',
    'ru' => 'Русский',
    'ua' => 'Українська',    
    'by' => 'Беларуская',
    'es' => 'Español',    
    'es' => 'Español',    
    'pt' => 'Português',
    'de' => 'Deutsch',    
    'fr' => 'Français',    
    'it' => 'Italiano',    
    'pl' => 'Polszczyzna',
    'pl' => 'Polszczyzna',
    'jp' => '日本語',    	
    'lt' => 'Lietuvių',    	    
    'lv' => 'Latviešu',
    'cz' => 'Čeština'    
);


// Определяем предпочтительный язык
function tryToFindLang($aLanguages, $sWhere, $sDefaultLang) {

    // Устанавливаем текущий язык как язык по умолчанию
    $sLanguage = $sDefaultLang;

    // Изначально используется лучшее качество
    $fBetterQuality = 0;

    // Поиск всех подходящих парметров
    preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i", $sWhere, $aMatches, PREG_SET_ORDER);
    foreach ($aMatches as $aMatch) {

        // Устанавливаем префикс языка
        $sPrefix = strtolower ($aMatch[1]);

        // Подготоваливаем временный язык
        $sTempLang = (empty($aMatch[3])) ? $sPrefix : $sPrefix . '-' . strtolower ($aMatch[3]);

        // Получаем значения качества (если оно есть)
        $fQuality = (empty($aMatch[5])) ? 1.0 : floatval($aMatch[5]);

        if ($sTempLang) {

            // Определяем наилучшее качество
            if ($fQuality > $fBetterQuality && in_array($sTempLang, array_keys($aLanguages))) {

                // Устанавливаем текущий язык как временный и обновляем значение качества
                $sLanguage = $sTempLang;
                $fBetterQuality = $fQuality;
            } elseif (($fQuality*0.9) > $fBetterQuality && in_array($sPrefix, array_keys($aLanguages))) {

                // Устанавливаем текущий язык как значение префикса и обновляем значение качества
                $sLanguage = $sPrefix;
                $fBetterQuality = $fQuality * 0.9;
            }
        }
    }
    return $sLanguage;
}
?>