<?php defined('_JEXEC') or die('Restricted access'); ?>
<h3>Welcome to Joda Demo!</h3>

<div style='color:red'>Volunteers wanted to write this article!</div>

<p>This is Joomla Component designed to introduce Joda - Joomla Database & SQL Abstraction layer.</p>

<p>Joda is a collection of classes:</p>

<h4>JQueryBuilder (abstract)</h4>

<P>This is the only class you can use separately, as a standalone SQL query building tool.</P>

<P>
When you create a JQueryBuilder object you specify the target SQL dialect, i.e. the prospected SQL server that is going to run your queries.
JQueryBuilder instances are created using factoring pattern by calling a JFactory's method. In fact you never get direct JQueryBuilder
instance (it is abstract class), but an instance of one of its child classes responsible for the corresponding SQL dialect.
</P>

<P>After you create the object you start "filling" the query sections: fileds, table names, join conditions, order clauses, limit, etc.</P>

<P>Final step is to retrieve the resulting SQL query</P>

<P>
Note please, JQueryBuilder is not a magic translator or at least not that much powerfull and it couldn't be. It is a tool attempting to circumvent
the diversity of SQL dialects (and standards) used nowdays. It emphasizes on isolating the differences in quoting, SQL functions incompatibility,
as well as all known! syntax deviations.
</P>

<P>To make things clear: you do not use JQueryBuilder by passing some weird SQL query (string) and receiving translated, valid SQL query (string)!</P>

<p>Anyway, to get the idea, see <a href='?option=com_jodademo&view=examples'>Examples!</a></p>


<P>Supported SQL statements:<BR><BR>

<code>
<BR>SELECT [DISTINCT] * | expression [ AS output_name ] [, ...]
[ FROM from_item [, ...] ]
[ WHERE condition ]
[ GROUP BY expression [, ...] ]
[ ORDER BY expression [ASC|DESC] ]
[ LIMIT-CLAUSE ]

<BR><BR>UPDATE table SET field=expression [, ...] [WHERE condition]

<BR><BR>INSERT INTO table () VALUES ()

<BR><BR>DELETE FROM table [WHERE condition] [LIMIT]
</code>



<h4>JConnection (abstract)</h4>
<P>While JQueryBuilder tackles SQL dialects, JConnection deals with the database server or servers, providing methods for executing queries,
having static container to keep list of active connections, handles transactions if told to do so and so on.
</P>

<P>You will find the term "named connection" among other things. Unlike current Joomla database layer, Joda introduces the neat idea of
having a preconfigured set of connections recognized by their names (unique). Each connection defines the location of the database it will connect to,
specific connection parameters, credentials (usernames and passwords) and, the most important, the database engine, i.e. MySQL, PostgreSQL, MSSQL, etc.
This way, many database servers can be used simultaneously in a single script.  That's what we should call a multidatabase CMS :-)
</P>


<h4>JDataset</h4>
<P>Getting a new JDataset object should be the starting point in your code. Once you get a JDataset object, you have everything on hand to:

<ul>
<li>Have a brand new JQueryBuilder instance
<li>Create SQL queries
<li>Execute SQL queries
<li>Retrieve and manage data
<li>Working with connection
<li>Getting database metadata
<li>etc.
</ul>

An important note: while you can create as many JDataset and JQueryBuilder objects, JConnection creation follows the singleton pattern,
instances being unique by their name, the connection name. Since JDataset has a JConnection property, many JDataset instances
share a single connection, as long as they requested the same connection name at the time of creation:
</P>
<BR>
<code>
$ds1 = JFactory::getDBSet("connectionONE");<BR>
$ds2 = JFactory::getDBSet("connectionONE");<BR>
</code>
<BR>
<P>In the example above you get TWO different JDataset objects, with their connection properties referencing the same connection instance.</P>


<h4>JRelation (abstract)</h4>

<P>A class extending JDataset to move the user point of view a little bit closer to database structure.
This class represents a single relation, that is, a table.</P>

<P>You never use it (abstract!) but always create objects of classes extending it, such as:

<ul>
<li>JRelationSection</li>
<li>JRelationUser</li>
<li>JRelationMenu</li>
<li>... you name it</li>
</ul>

Usage of classes listed above is easy to guess: they represent section table, users table, menu table, and so on.
</P>


<P><BR><B><I>... to be continued</I></B></P>

<P><BR><P><BR><P><BR><P><BR>