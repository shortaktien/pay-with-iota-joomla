<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.paywithiota
 *
 * @copyright   Copyright (C) 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/**
 * Plugin to add paywithiota button into content (e.g. articles)
 * This uses the {iota} syntax
 *
 * @since  1.5
 */
class PlgContentPaywithiota extends JPlugin
{

    /**
     * Plugin that add paywithiota button within content
     *
     * @param string $context The context of the content being passed to the plugin.
     * @param object   &$article The article object.  Note $article->text is also available
     * @param mixed    &$params The article params
     * @param integer $page The 'page' number
     *
     * @return  mixed   true if there is an error. Void otherwise.
     *
     * @since   1.6
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        // Don't run this plugin when the content is being indexed
        if ($context === 'com_finder.indexer') {
            return true;
        }

        // Simple performance check to determine whether bot should process further
        if (strpos($article->text, 'iotabtn') === false && strpos($article->text, 'fiatbtn') === false && strpos($article->text, 'donationbtn') === false) {
            return true;
        }
        $document = JFactory::getDocument();
        $document->addScript("https://shortaktien.de/build/iota-button.esm.js",array(), ['type' =>'module']);
        $document->addScript("https://shortaktien.de/build/iota-button.js",array(),['nomodule' =>'nomodule']);

        // Expression to search for (iotabtn)
        $regexiota = '/{iotabtn}/i';

        // Expression to search for(fiatbtn)
        $regexfiat = '/{fiatbtn}/i';

        // Expression to search for(donationbtn)
        $regexdonation = '/{donationbtn}/i';

        $address = $this->params->get('iota_address', '');
        $amount = $this->params->get('iota_amount','');
        $currency = $this->params->get('iota_currency','');
        $user = $this->params->get('iota_user','');

        // Find all instances of plugin and put in $matches for iotabtn
        preg_match_all($regexiota, $article->text, $matches, PREG_SET_ORDER);
        if ($matches)
        {
            foreach ($matches as $match)
            {
                $output = '<iota-button address="'.$address.'" amount="'.$amount.'"></iota-button>';
                if (($start = strpos($article->text, $match[0])) !== false)
                {
                    $article->text = substr_replace($article->text, $output, $start, strlen($match[0]));
                }
            }
        }

        // Find all instances of plugin and put in $matches for fiatbtn
        preg_match_all($regexfiat, $article->text, $matchesfiat, PREG_SET_ORDER);
        if ($matchesfiat)
        {
            foreach ($matchesfiat as $match)
            {
                $output = '<iota-button address="'.$address.'"
										amount="'.$amount.'"
										currency="'.$currency.'">
										</iota-button>';
                if (($start = strpos($article->text, $match[0])) !== false)
                {
                    $article->text = substr_replace($article->text, $output, $start, strlen($match[0]));
                }
            }
        }

        // Find all instances of plugin and put in $matches for donationbtn
        preg_match_all($regexdonation, $article->text, $matchesdonation, PREG_SET_ORDER);
        if ($matchesdonation)
        {
            foreach ($matchesdonation as $match)
            {
                $output = '<iota-button address="'.$address.'"
					 currency="'.$currency.'"
					 label="Donate"
					 merchant="'.$user.'"
					 type="donation">
					 </iota-button>';
                if (($start = strpos($article->text, $match[0])) !== false)
                {
                    $article->text = substr_replace($article->text, $output, $start, strlen($match[0]));
                }
            }
        }
    }
}
