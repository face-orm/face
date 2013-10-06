face
====

Still under development and testing - Not ready for production.

why ?
--------

Face is an ORM that was started from the need of having something easy to setup,
easy to use and reasonable in performances. But that still do the job of an ORM. 
The big needing was to be able to do join statement simplely while keeping all parent/children relations.

The job is almost successful. The ORM does the job very well 
and it does what we ask it to do, without any bad suprises.

Performances have not been working in depth for the moment, but basically it does well.
Then we are very confident about this point.


places
--------

Site and docs are available at : http://face-orm.org (documentation is still being written)

simple benchmark is available at : https://github.com/laemons/ORM-benchmark



Roadmap
---------

Important
 * many to many seamless relationship
 * implied hydration
 * cache model implementation
 * performances updates on repetitive tasks
 * chain update/insert/delete
 * improving FaceQL
 * support for subquery
 * support for transactions
 * support for debug optimisation
 * fast queryall/queryone

Later
 * annotation models reader
 * face admin - crud
 * easy cache
 * graphical generator/customizer/visualizer
 * graphical grid editor api (e.g for jquery datatable)

Future
 * embryo for workbench .mwb files