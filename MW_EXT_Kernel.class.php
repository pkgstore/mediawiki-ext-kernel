<?php

namespace MediaWiki\Extension\PkgStore;

use ConfigException;
use MWException;
use OutputPage, PPFrame, RequestContext, Skin;
use Title, User, WikiPage;

/**
 * Class MW_EXT_Kernel
 */
class MW_EXT_Kernel
{
  /**
   * Clear DATA (escape html).
   *
   * @param $string
   *
   * @return string
   */
  public static function outClear($string): string
  {
    $trim = trim($string);
    return htmlspecialchars($trim, ENT_QUOTES);
  }

  /**
   * Normalize DATA (lower case & remove space).
   *
   * @param $string
   *
   * @return string
   */
  public static function outNormalize($string): string
  {
    $replace = str_replace(' ', '-', $string);
    return mb_strtolower($replace, 'UTF-8');
  }

  /**
   * Get JSON data.
   *
   * @param $src
   *
   * @return mixed
   */
  public static function getJSON($src): mixed
  {
    $src = file_get_contents($src);
    return json_decode($src, true);
  }

  /**
   * Get YAML data.
   *
   * @param $src
   * @return mixed
   */
  public static function getYAML($src): mixed
  {
    return yaml_parse_file($src);
  }

  /**
   * Wiki Framework: Message.
   *
   * @param $id
   * @param $key
   *
   * @return string
   */
  public static function getMessageText($id, $key): string
  {
    $string = 'mw-' . $id . '-' . $key;
    $message = wfMessage($string)->inContentLanguage();
    return $message->text();
  }

  /**
   * Wiki Framework: Configuration parameters.
   *
   * @param $config
   *
   * @return mixed
   * @throws ConfigException
   */
  public static function getConfig($config): mixed
  {
    $context = RequestContext::getMain()->getConfig();
    return $context->get($config);
  }

  /**
   * Wiki Framework: Title.
   *
   * @return Title|null
   */
  public static function getTitle(): ?Title
  {
    $context = RequestContext::getMain();
    return $context->getTitle();
  }

  /**
   * Wiki Framework: User.
   *
   * @return User|null
   */
  public static function getUser(): ?User
  {
    $context = RequestContext::getMain();
    return $context->getUser();
  }

  /**
   * Wiki Framework: WikiPage.
   *
   * @return WikiPage
   * @throws MWException
   */
  public static function getWikiPage(): WikiPage
  {
    $context = RequestContext::getMain();
    return $context->getWikiPage();
  }

  /**
   * Converts an array of values in form [0] => "name=value" into a real
   * associative array in form [name] => value. If no = is provided,
   * true is assumed like this: [name] => true.
   *
   * @param array $options
   * @param PPFrame $frame
   *
   * @return array
   */
  public static function extractOptions(PPFrame $frame, array $options = []): array
  {
    $results = [];

    foreach ($options as $option) {
      $pair = explode('=', $frame->expand($option), 2);

      if (count($pair) === 2) {
        $name = MW_EXT_Kernel::outClear($pair[0]);
        $value = MW_EXT_Kernel::outClear($pair[1]);
        $results[$name] = $value;
      }

      if (count($pair) === 1) {
        $name = MW_EXT_Kernel::outClear($pair[0]);
        $results[$name] = true;
      }
    }

    return $results;
  }

  /**
   * Load resource function.
   *
   * @param OutputPage $out
   * @param Skin $skin
   *
   * @return void
   */
  public static function onBeforePageDisplay(OutputPage $out, Skin $skin): void
  {
    $out->addModuleStyles(['ext.mw.kernel.styles']);
  }
}
