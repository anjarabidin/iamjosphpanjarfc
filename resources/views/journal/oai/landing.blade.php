<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAI 2.0 Request Results</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 16px; margin: 20px; color: #000; }
        h1 { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        h2 { font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; }
        .nav-links { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; font-size: 14px; }
        .nav-links a { color: #0000CC; text-decoration: underline; margin: 0 2px; }
        .nav-links a:first-child { margin-left: 0; }
        .label-cell { background-color: #e0e0ff; font-weight: bold; text-align: right; padding: 3px 10px; width: 150px; vertical-align: top; white-space: nowrap; }
        .value-cell { background-color: #fff; padding: 3px 5px; vertical-align: top; }
        table.info-table { border-collapse: separate; border-spacing: 2px; margin-bottom: 10px; font-size: 14px; width: auto; }
        .error-msg { margin-top: 15px; margin-bottom: 20px; font-size: 16px; }
        .about-text { font-size: 14px; margin-bottom: 10px; line-height: 1.5; }
        .about-text a { color: #0000CC; text-decoration: underline; }
    </style>
</head>
<body>
    <h1>OAI 2.0 Request Results</h1>

    <div class="nav-links">
        <a href="?verb=Identify">Identify</a> | 
        <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a> | 
        <a href="?verb=ListSets">ListSets</a> | 
        <a href="?verb=ListMetadataFormats">ListMetadataFormats</a> | 
        <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
    </div>

    <p class="about-text" style="margin-bottom: 20px;">
        You are viewing an HTML version of the XML OAI response. To see the underlying XML use your web browsers view source option. More information about this XSLT is at the <a href="#about">bottom of the page</a>.
    </p>

    <table class="info-table">
        <tr>
            <td class="label-cell">Datestamp of response</td>
            <td class="value-cell">{{ now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z') }}</td>
        </tr>
        <tr>
            <td class="label-cell">Request URL</td>
            <td class="value-cell">{{ url()->current() }}</td>
        </tr>
    </table>

    <h2>OAI Error(s)</h2>
    <p style="margin-bottom: 15px;">The request could not be completed due to the following error or errors.</p>

    <table class="info-table">
        <tr>
            <td class="label-cell">Error Code</td>
            <td class="value-cell">badVerb</td>
        </tr>
    </table>
    
    <div class="error-msg">
        Illegal OAI verb
    </div>

    <hr style="border: 0; border-top: 1px solid #ccc; margin-top: 30px; margin-bottom: 5px;">
    <div class="nav-links" style="border-top: none; padding-top: 0; margin-bottom: 30px;">
        <a href="?verb=Identify">Identify</a> | 
        <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a> | 
        <a href="?verb=ListSets">ListSets</a> | 
        <a href="?verb=ListMetadataFormats">ListMetadataFormats</a> | 
        <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
    </div>

    <h2 id="about" style="margin-top: 0;">About the XSLT</h2>
    <p class="about-text">
        An XSLT file has converted the <a href="http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd" target="_blank">OAI-PMH 2.0</a> responses into XHTML which looks nice in a browser which supports XSLT such as Mozilla, Firebird and Internet Explorer. The XSLT file was created by <a href="http://www.gutteridge.org/" target="_blank">Christopher Gutteridge</a> at the University of Southampton as part of the <a href="http://www.gnu.org/software/eprints/" target="_blank">GNU EPrints</a> system, and is freely redistributable under the <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPL</a>.
    </p>
    <p class="about-text">
        If you want to use the XSL file on your own OAI interface you may but due to the way XSLT works you must install the XSL file on the same server as the OAI script, you can't just link to this copy.
    </p>
    <p class="about-text">
        For more information or to download the XSL file please see the <a href="http://www.openarchives.org/pmh/tools/xslt/" target="_blank">OAI to XHTML XSLT homepage</a>.
    </p>

</body>
</html>
