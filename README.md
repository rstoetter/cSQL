# csql-php

The class cSQL is written in PHP and helps to manage SQL statement components. 

The namespace is rstoetter\\cSql

## static methods provided by the class cSQL

    cSQL::AddFunction()
    cSQL::CheckedFieldName()
    cSQL::CombineFieldName()
    cSQL::DotCount()
    cSQL::FieldNameIn()
    cSQL::FormatStatement()
    cSQL::GetAliasFromTablename()
    cSQL::GetAliasOrRealFieldname()
    cSQL::GetRealFieldname()
    cSQL::HasAlias()
    cSQL::IsEscaped()
    cSQL::IsExtraStartIdentifier()
    cSQL::IsFunctionSQL()
    cSQL::IsJoin()
    cSQL::IsQualified()
    cSQL::IsSqlZeroDate()
    cSQL::IsSqlZeroDateTime()
    cSQL::IsSqlZeroTime()
    cSQL::IsSubquerySQL()
    cSQL::IsSubqueryStatement()
    cSQL::IsValidFieldName()
    cSQL::PositionEndFunctionSQL()
    cSQL::RemoveAlias()
    cSQL::RemoveAll()
    cSQL::RemoveEscapes()
    cSQL::RemoveFunctions()
    cSQL::RemoveQualification()
    cSQL::SchemaNameIn()
    cSQL::SetExtraIdentifier()
    cSQL::SetExtraStartIdentifier()
    cSQL::SplitQualifiedFieldname()
    cSQL::SqlDate2DateTime()
    cSQL::SqlDateTime2DateTime()
    cSQL::SqlTime2DateTime()
    cSQL::TableNameIn()

## Installation

This project assumes you have composer installed. Simply add:

    "require" : {

        "rstoetter/csql-php" : ">=1.0.0"

    }

to your composer.json, and then you can simply install with:

    composer install

## more information

See the [project wiki](https://github.com/rstoetter/csql-php/wiki) for more information.

