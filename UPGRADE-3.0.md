UPGRADE FROM 2.x to 3.0
=======================

 * Translation catalogues were renamed from "messages" to "HackzillaTicketBundle"
   in order to allow projects consuming this bundle to override them in a proper
   way.

 * Renamed method `TicketManagerInterface::getTicketList()` to `TicketManagerInterface::getTicketListQuery()`
   to clarify what it returns.

 * `TicketManagerInterface::updateTicket()` now returns `void`, since the object
   (`TicketInterface`) it was returning before is the same as the provided in its
   argument 1.
