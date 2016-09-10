<?php namespace App\Twig;

use Twig_TokenParser;
use Twig_Token;
use App\Twig\CacheNode;

class CacheTokenParser extends Twig_TokenParser {

    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        // $key = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $key = $this->parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(function(Twig_Token $token) {
            return $token->test('endcache');
        }, true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new CacheNode($key, $body, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'cache';
    }

}