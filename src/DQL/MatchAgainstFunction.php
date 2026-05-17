<?php

declare(strict_types=1);

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * @see https://dev.mysql.com/doc/refman/8.4/en/fulltext-search.html
 *
 * MatchAgainstFunction ::= MATCH (col1,col2,...) AGAINST (expr [search_modifier])
 * search_modifier ::= IN BOOLEAN MODE | [IN NATURAL LANGUAGE MODE] WITH QUERY EXPANSION
 */
class MatchAgainstFunction extends FunctionNode
{
    /**
     * @var PathExpression[]
     */
    private array $columns = [];

    private Literal|InputParameter $expression;

    private bool $inBooleanMode = false;

    private bool $withQueryExpansion = false;


    #[\Override]
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // MATCH
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->parseColumns($parser);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);

        $this->parseAll($parser, 'AGAINST');
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->parseSearchExpression($parser);
        $this->parseSearchModifier($parser);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    #[\Override]
    public function getSql(SqlWalker $walker): string
    {
        $columns = \join(', ', \array_map(static fn($c) => $c->dispatch($walker), $this->columns));
        $expression = $this->expression->dispatch($walker);
        $modifier = match (true) {
            $this->inBooleanMode => ' IN BOOLEAN MODE',
            $this->withQueryExpansion => ' WITH QUERY EXPANSION',
            default => '',
        };

        return "MATCH ({$columns}) AGAINST ({$expression}{$modifier})";
    }

    private function parseColumns(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $this->columns[] = $parser->StateFieldPathExpression();
        while ($lexer->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->columns[] = $parser->StateFieldPathExpression();
        }
    }

    private function parseAll(Parser $parser, string ...$keywords): void
    {
        $lexer = $parser->getLexer();
        foreach ($keywords as $kw) {
            $token = $lexer->lookahead;
            if ($token?->isA(TokenType::T_IDENTIFIER) && \strcasecmp($token->value, $kw) === 0) {
                $lexer->moveNext();
                continue;
            }
            $parser->syntaxError($kw, $token);
        }
    }

    private function parseAny(Parser $parser, string ...$keywords): ?string
    {
        $lexer = $parser->getLexer();
        $token = $lexer->lookahead;
        if ($token !== null && $token->isA(TokenType::T_IDENTIFIER)) {
            foreach ($keywords as $kw) {
                if (\strcasecmp($token->value, $kw) === 0) {
                    $lexer->moveNext();
                    return $kw;
                }
            }
        }

        return null;
    }

    private function parseSearchExpression(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(TokenType::T_STRING)) {
            $this->expression = $parser->Literal();
        } elseif ($lexer->isNextToken(TokenType::T_INPUT_PARAMETER)) {
            $this->expression = $parser->InputParameter();
        } else {
            $parser->syntaxError('string literal or input parameter');
        }
    }

    private function parseSearchModifier(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(TokenType::T_IN)) {
            $lexer->moveNext();
            switch ($this->parseAny($parser, 'BOOLEAN', 'NATURAL')) {
                case 'BOOLEAN':
                    $this->parseAll($parser, 'MODE');
                    $this->inBooleanMode = true;
                    break;
                case 'NATURAL':
                    $this->parseAll($parser, 'LANGUAGE', 'MODE');
                    break;
                default:
                    $parser->syntaxError('BOOLEAN MODE or NATURAL LANGUAGE MODE');
            }
        }

        if ($this->inBooleanMode) {
            return;
        }

        if ($lexer->isNextToken(TokenType::T_WITH)) {
            $lexer->moveNext();
            $this->parseAll($parser, 'QUERY', 'EXPANSION');
            $this->withQueryExpansion = true;
        }
    }
}
