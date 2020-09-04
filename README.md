



<h1>Projet 7 Openclassrooms- BileMo</h1>
    <p>BileMo Api developpement project
             <a href="https://codeclimate.com/github/JgPhil/BileMo/maintainability"><img src="https://api.codeclimate.com/v1/badges/449883de8d8b0bc1c40a/maintainability" /></a>
    </p>
    <h2>Environment used during development</h2>
    <ul>
        <li>
            <p>Apache 2.4.41</p>
        </li>
        <li>
            <p>PHP 7.4.3</p>
        </li>
        <li>
            <p>MySQL 8.0.2</p>
        </li>
        <li>
            <p>Composer 1.10.1</p>
        </li>
        <li>
            <p>Git 2.25.1</p>
        </li>
        <li>
            <p>Symfony 5.1</p>
        </li>
    </ul>
    <h2>Installation</h2>
    <h3>Environment setup</h3>
    <p>It is necessary to have an Apache / Php / Mysql environment.<br>
        Depending on your operating system, several servers can be installed:</p>
    <ul>
        <li>
            <p>Windows : WAMP (<a href="http://www.wampserver.com/" rel="nofollow">http://www.wampserver.com/</a>)</p>
        </li>
        <li>
            <p>MAC : MAMP (<a href="https://www.mamp.info/en/mamp/" rel="nofollow">https://www.mamp.info/en/mamp/</a>)
            </p>
        </li>
        <li>
            <p>Linux : LAMP (<a href="https://doc.ubuntu-fr.org/lamp" rel="nofollow">https://doc.ubuntu-fr.org/lamp</a>)
            </p>
        </li>
        <li>
            <p>Cross system: XAMP (<a href="https://www.apachefriends.org/fr/index.html"
                    rel="nofollow">https://www.apachefriends.org/fr/index.html</a>)</p>
        </li>
    </ul>
    <p>Symfony 5.1 requires PHP 7.2.5 or higher to run.<br>
        Prefer to have MySQL 5.6 or higher.<br>
        Make sure PHP is in the Path environment variable if you are on a Windows system.<br>
        Note that PHP must have the extension mb_string activated for the slug converter to work.</p>
    <p>You need an installation of Composer.<br>
        So, install it if you don't have it. (<a href="https://getcomposer.org/"
            rel="nofollow">https://getcomposer.org/</a>)</p>
    <p>If you want to use Git (optional), install it. (<a href="https://git-scm.com/downloads"
            rel="nofollow">https://git-scm.com/downloads</a>)</p>
    <h3>Project files local deployement</h3>
    <p>Manually download the content of the Github repository to a location on your file system.<br>
        You can also use git.<br>
        In Git, go to the chosen location and execute the following command:</p>
    <pre><code>git clone https://github.com/JgPhil/BileMo.git .</code></pre>
    <p>Open a command console and join the application root directory.<br>
        Install dependencies by running the following command:</p>
    <pre><code>composer install</code></pre>

<h3>Database generation</h3>
<p>Change the database connection values for correct ones in the .env file.<br>
Like the following example with a snowtricks named database to create:</p>
<pre><code>DATABASE_URL=mysql://root:@127.0.0.1:3306/bilemo?serverVersion=5.7
</code></pre>
<p>In a new console placed in the root directory of the application;<br>
Launch the creation of the database:</p>
<pre><code>php bin/console doctrine:database:create
</code></pre>
<p>Then, build the database structure using the following command:</p>
<pre><code>php bin/console doctrine:migrations:migrate
</code></pre>
<p>Finally, load the initial dataset into the database :</p>
<pre><code>php bin/console doctrine:fixtures:load
</code></pre>

<h3>LexikJWTAuthenticationBundle installation & configuration</h3>
<h4>Installation</h4>
<p>The bundle is automatically installed with composer, but we have some more manual work here.</p>

</a>Generate the SSH keys:</h4>
<div class="highlight highlight-source-shell"><pre>$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout</pre></div>

<p>Configure the SSH keys path in your <code>config/packages/lexik_jwt_authentication.yaml</code> :</p>
<div class="highlight highlight-source-yaml"><pre><span class="pl-ent">lexik_jwt_authentication</span>:
    <span class="pl-ent">secret_key</span>:       <span class="pl-s"><span class="pl-pds">'</span>%kernel.project_dir%/config/jwt/private.pem<span class="pl-pds">'</span></span> <span class="pl-c"><span class="pl-c">#</span> required for token creation</span>
    <span class="pl-ent">public_key</span>:       <span class="pl-s"><span class="pl-pds">'</span>%kernel.project_dir%/config/jwt/public.pem<span class="pl-pds">'</span></span>  <span class="pl-c"><span class="pl-c">#</span> required for token verification</span>
    <span class="pl-ent">pass_phrase</span>:      <span class="pl-s"><span class="pl-pds">'</span>your_secret_passphrase<span class="pl-pds">'</span></span> <span class="pl-c"><span class="pl-c">#</span> required for token creation, usage of an environment variable is recommended</span>
    <span class="pl-ent">token_ttl</span>:        <span class="pl-c1">3600</span></pre></div>
<p>Configure your <code>config/packages/security.yaml</code> :</p>
<div class="highlight highlight-source-yaml"><pre><span class="pl-ent">security</span>:
    <span class="pl-c"><span class="pl-c">#</span> ...</span>    
    <span class="pl-ent">firewalls</span>:
        <span class="pl-ent">login</span>:
            <span class="pl-ent">pattern</span>:  <span class="pl-s">^/api/login</span>
            <span class="pl-ent">stateless</span>: <span class="pl-c1">true</span>
            <span class="pl-ent">anonymous</span>: <span class="pl-c1">true</span>
            <span class="pl-ent">json_login</span>:
                <span class="pl-ent">check_path</span>:               <span class="pl-s">/api/login_check</span>
                <span class="pl-ent">success_handler</span>:          <span class="pl-s">lexik_jwt_authentication.handler.authentication_success</span>
                <span class="pl-ent">failure_handler</span>:          <span class="pl-s">lexik_jwt_authentication.handler.authentication_failure</span>
 <span class="pl-ent">api</span>:
            <span class="pl-ent">pattern</span>:   <span class="pl-s">^/api</span>
            <span class="pl-ent">stateless</span>: <span class="pl-c1">true</span>
            <span class="pl-ent">guard</span>:
                <span class="pl-ent">authenticators</span>:
                    - <span class="pl-s">lexik_jwt_authentication.jwt_token_authenticator</span>
    <span class="pl-ent">access_control</span>:
        - <span class="pl-s">{ path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }</span>
        - <span class="pl-s">{ path: ^/api,       roles: IS_AUTHENTICATED_FULLY }</span></pre></div>
<p>Configure your routing into <code>config/routes.yaml</code> :</p>
<div class="highlight highlight-source-yaml"><pre><span class="pl-ent">api_login_check</span>:
    <span class="pl-ent">path</span>: <span class="pl-s">/api/login_check</span></pre></div>
<h3>Run the web application</h3>
<h4>By WebServerBundle</h4>
<p>Launch the Apache/Php runtime environment by using Symfony via the following command:</p>
<pre><code>php bin/console serve -d
</code></pre>
<p>
Then consult the documentation at this URL <a href="http://localhost:8000/swagger/" rel="nofollow">http://localhost:8000/swagger/</a> from your browser.
</p>
<h3>Login credentials</h3>
<p>You can access to the administrator apis with this credentials:</p>
<ul>
    <li>username: admin</li>
    <li>password: password</li>
</ul>
<p>You can access to the user apis with this credentials:</p>
<ul>
    <li>username: customer</li>
    <li>password: password</li>
</ul>
