<?php
include_once 'database.php';
// Check connection
if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($conn,"SELECT * FROM test");

echo "<table border='1'>
<tr>
<th>discomfort</th>
<th>fatigue</th>
<th>headache</th>
<th>eyestrain</th>
<th>saliva</th>
<th>sweat</th>
<th>nausea</th>
<th>focus</th>
<th>fullness</th>
<th>blurred</th>
<th>dizzinessEyes</th>
<th>dizzyclose</th>
<th>vertigo</th>
<th>stomach</th>
<th>burp</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $_row['discomfort'] . "</td>";
echo "<td>" . $_row['fatigue'] . "</td>";
echo "<td>" . $_row['headache'] . "</td>";
echo "<td>" . $_row['eyestrain'] . "</td>";
echo "<td>" . $_row['saliva'] . "</td>";
echo "<td>" . $_row['sweat'] . "</td>";
echo "<td>" . $_row['nausea'] . "</td>";
echo "<td>" . $_row['focus'] . "</td>";
echo "<td>" . $_row['fullness'] . "</td>";
echo "<td>" . $_row['blurred'] . "</td>";
echo "<td>" . $_row['dizzinessEyes'] . "</td>";
echo "<td>" . $_row['dizzyclose'] . "</td>";
echo "<td>" . $_row['vertigo'] . "</td>";
echo "<td>" . $_row['stomach'] . "</td>";
echo "<td>" . $_row['burp'] . "</td>";
echo "</tr>";
}
echo "</table>";

mysqli_close($conn);
?>

