SELECT
	companies.*,
	(
		SELECT date_in
		FROM interactions
		WHERE interactions.companyID = companies.ID
		ORDER BY date_in DESC
		LIMIT 0, 1) AS group_heading,
	JSON_UNQUOTE(JSON_EXTRACT(`companies`.`data`, '$.f1')) AS f1,
	GROUP_CONCAT(DISTINCT table_join_f2.value) AS f2,
	GROUP_CONCAT(DISTINCT table_join_f5.value) AS f5,
	JSON_UNQUOTE(JSON_EXTRACT(`companies`.`data`, '$.f6')) AS f6,
	GROUP_CONCAT(DISTINCT table_join_f7.value) AS f7,
	(
		SELECT date_in
		FROM interactions
		WHERE interactions.companyID = companies.ID
		ORDER BY date_in DESC
		LIMIT 0, 1) AS f8
FROM companies
	LEFT JOIN (
							SELECT
								interactions.ID AS ID,
								interactions.label AS value
							FROM interactions
								INNER JOIN interaction_types ON interaction_types.ID = interactions.typeID) table_join_f2
		ON FIND_IN_SET(table_join_f2.`ID`, JSON_UNQUOTE(JSON_EXTRACT(`companies`.`data`, '$.f2')))
	LEFT JOIN `fields_companies_data` table_join_f5
		ON FIND_IN_SET(table_join_f5.`ID`, JSON_UNQUOTE(JSON_EXTRACT(`companies`.`data`, '$.f5')))
	LEFT JOIN `fields_companies_data` table_join_f7
		ON FIND_IN_SET(table_join_f7.`ID`, JSON_UNQUOTE(JSON_EXTRACT(`companies`.`data`, '$.f7')))
WHERE _deleted = '0'
GROUP BY companies.ID
ORDER BY (
	SELECT date_in
	FROM interactions
	WHERE interactions.companyID = companies.ID
	ORDER BY date_in DESC
	LIMIT 0, 1) ASC, date_in DESC