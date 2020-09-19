<?php
namespace Danack\PropertyTaint;

use PhpParser;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\DocComment;
use Psalm\FileManipulation;
use Psalm\StatementsSource;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Plugin\Hook\AfterClassLikeAnalysisInterface;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\PropertyProperty;

class PropertyTainter implements AfterClassLikeAnalysisInterface
{
    private static function addDetectedTaint(
        StatementsSource $statements_source,
        Codebase $codebase,
        PropertyProperty $prop,
        array $info
    ) {
        $expr_type = $statements_source->getNodeTypeProvider()->getType($prop);

        // should be a globally unique id
        // you can use its line number/start offset
        $expr_identifier = 'tainted_property'
            . '-' . $statements_source->getFileName()
            . ':' . $prop->getAttribute('startFilePos');

        $codebase->addTaintSource(
            $expr_type,
            $expr_identifier,
            $info,
            new CodeLocation($statements_source, $prop)
        );
    }

    public static function handleTag(
        StatementsSource $statements_source,
        Codebase $codebase,
        \PhpParser\Node\Stmt\PropertyProperty $prop,
        string $tag_type,
        array $info
    ) {
        if ($tag_type !== 'psalm-taint-source') {
            return;
        }

        self::addDetectedTaint(
            $statements_source,
            $codebase,
            $prop,
            $info
        );
    }

    public static function handleStatement(
        StatementsSource $statements_source,
        Codebase $codebase,
        \PhpParser\Node\Stmt $statement
    ) {
        if (!$statement instanceof \PhpParser\Node\Stmt\Property) {
            return;
        }

        foreach ($statement->props as $prop) {
            $docblock = $statement->getDocComment();

            if ($docblock) {
                $parsed_docblock = DocComment::parsePreservingLength($docblock);
            }
            else {
                $parsed_docblock = new \Psalm\Internal\Scanner\ParsedDocblock('', []);
            }

            foreach ($parsed_docblock->tags as $tag_type => $info) {
                self::handleTag(
                    $statements_source,
                    $codebase,
                    $prop,
                    $tag_type,
                    $info
                );
            }
        }
    }

    /**
     * Called after an expression has been checked
     *
     * @param PhpParser\Node\Expr $expr
     * @param Context             $context
     * @param string[]            $suppressed_issues
     * @param FileManipulation[]  $file_replacements
     *
     * @return void
     */
    public static function afterStatementAnalysis(
        ClassLike $stmt,
        ClassLikeStorage $classlike_storage,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = []
    ) {
        if ($stmt instanceof PhpParser\Node\Stmt\Class_) {
            foreach ($stmt->stmts as $statement) {
                self::handleStatement(
                    $statements_source,
                    $codebase,
                    $statement
                );
            }
        }
    }
}