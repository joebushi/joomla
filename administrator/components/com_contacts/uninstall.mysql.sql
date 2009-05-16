-- $Id$

-- --------------------------------------------------------

--
-- Dropping tables
--
DROP TABLE #__contacts_contacts;
DROP TABLE #__contacts_con_cat_map;
DROP TABLE #__contacts_details;
DROP TABLE #__contacts_fields;

--
-- Deleting records
--

DELETE FROM #__categories WHERE extension = 'com_contacts';