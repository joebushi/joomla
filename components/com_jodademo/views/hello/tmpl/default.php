<?php defined('_JEXEC') or die('Restricted access'); ?>
<h3>Welcome to Joda Demo Component!</h3>

This is a Joomla Component designed to introduce Joda - Joomla Database & SQL Abstraction layer.

Joda is a collection of classes:

<h4>JQueryBuilder</h4>

<P>This is the only class you can use separately, as a standalone SQL query building tool.</P>

<P>
When you create a JQueryBuilder object you specify the target SQL dialect, i.e. the prospected SQL server that is going to run your queries.
JQueryBuilder instances are created using factoring pattern by calling a JFactory method. In fact you never get direct JQueryBuilder
instance (it is abstract class), but an instance of one of its child classes responsible for the corresponding SQL dialect.
</P>

<P>
JQueryBuilder is not a magic translator or at least not that much powerfull and it couldn't be. It is a tool attempting to circumvent
the diversity of SQL dialects (and standards) used nowdays. The price you pay using JQueryBuilder is relatively high, but still worthy.
Because:
</P>


<h4>JConnection</h4>

<h4>JDataset</h4>

