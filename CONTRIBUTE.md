# How to contribute

We are always looking for people who want to contribute to the webSPELL codebase.
But there are some guidelines which should be followed to make the process as smooth
as possible.

## Getting Started

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Submit a ticket for your issue, assuming one does not already exist.
  * Clearly describe the issue including steps to reproduce when it is a bug.
  * Make sure you fill in the earliest version that you know has the issue.
* Fork the repository on GitHub.

## Creating Development/Testing setup

* Download and install [Vagrant](https://www.vagrantup.com/downloads.html)
* Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
* Clone your forked repository.
* Run `vagrant up` in the root of the cloned repo.
  * This will now create a VirtualBox, with all the required dev tools.
  * You will be asked to choose a NIC for a public network.
    * If DHCP is configured within your network, the VM will get a public IP.
  * Once done, you can access the setup in different ways:
    * SSH using `vagrant ssh`
    * SSH using port 2222 on localhost, `ssh -p 2222 vagrant@127.0.0.1`
      * Password is `vagrant`
    * HTTP using `127.0.0.1:8080`
    * HTTP using the public IP, if one has been assigned using DHCP.
      * Check with `ifconfig -a`
  * MySQL is also running with a DB to use for install.
    * DB: `webspelldev`
    * Username: `webspelldev`
    * Password: `webspelldev`
  * The sources are available in `/vagrant`, which is also the Apache docroot.
    * This is a synced folder from your local repository.
      Changes to the repository will be instantly visible in the VM.
* If you don't need the VM anymore, you can destroy it with `vagrant destroy`

## Making Changes

* Create a topic branch from where you want to base your work.
  * This is usually the dev branch.
  * To quickly create a topic branch based on dev; `git checkout -b
    fix/dev/issue-123-myfix dev`. Please avoid working directly on the
    `dev` branch.
* Make commits only for this specific topic, don't mix with other changes.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Make sure your commit messages are in the proper format.
  [Commit Format](https://github.com/webSPELL/webSPELL/issues/88#issuecomment-71665865)

````
    chore(docs): Add Contributing guidelines

    This example should show how a commit message should look like.
    The details block starting from the 3rd row is optional.
    It could explain more details of a single commit if required.
````

* Run `grunt codecheck` to assure correct codestyle is used.

## Submitting Changes

* Push your changes to a topic branch in your fork of the repository.
  `git push --set-upstream origin fix/dev/issue-123`
* Submit a pull request to the repository in the webspell organization.
  The PR should include the issue # you are fixing with this.

````
    fix(docs): Typo in README

    There was a type in the README.
    This fixes #123
````

* The core team looks at Pull Requests on a regular basis.
* We will review the PR, and open tasks for changes if any required.
* Once all tasks are completed PR will be approved and merged.

# Additional Resources

* [General GitHub documentation](https://help.github.com/)
* [GitHub pull request documentation](https://help.github.com/send-pull-requests/)
* #webspell IRC channel on quakenet.org
* [webspell-dev mailing list](https://groups.google.com/forum/#!forum/webspell-dev)
